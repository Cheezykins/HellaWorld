<?php

/**
 * cron.php - AutoNZB cron task to check for new episodes.
 * $Id$
 */
require_once('config.php');
require_once('classes/HellaController.php');
$controller = new HellaController($config['host'], $config['port'], $config['username'], $config['password']);

require_once('classes/xml-dataset.php');
require_once('classes/atom-feed.php');
require_once('classes/nzb-helper.php');

$dataset = new XML_Dataset('feeds.xml');

// Have a look for command-line options.
array_shift($_SERVER['argv']); // Drop the cron.php arg.
$args = array_map('strtolower', $_SERVER['argv']);

if (in_array('--help', $args))
	die("Usage: php cron.php [-v] [Show Name] [Show Name], ...\n");

$verbose = in_array('-v', $args);

// Remove the -v arg.
if ($verbose)
	unset($args[array_search('-v', $args)]);

// Flag to track if we did any work.
$did_run = FALSE;

// Loop over each feed, parse it, see if the latest episode has changed.
foreach ($dataset->feed as $feed) {
	$attrs =& $feed->attributes();
	
	// Only fetch the ones specified, if any.
	if ((bool) count($args)) {
		if (! in_array(strtolower($attrs->name), $args)) {
			$verbose AND printf("Skipping %s\n", $attrs->name);
			continue;
		};
	};
	
	// Make a note that we did some work.
	$did_run = TRUE;
	
	// Fetch the data from newzbin.
	$verbose AND printf("Fetching feed for %s\n", $attrs->name);
	$atom = new Atom_Feed($attrs->url);
	$latest = $atom->entry[0];
	$parsed = nzb::parse($latest->title);
	
	// Is the 1st item newer than the latest we recorded?
	if ($parsed->episode > $attrs->latest_episode) {
		echo sprintf('Queueing new episode of %s (%s)',
				$attrs->name, $parsed->episode), "\n";
		
		// Grab the nzb id off the url.
		$nzb_id = end(explode('/', substr($latest->id, 0, -1)));
		
		try {
			$controller->enqueueNewzbin($nzb_id);
			$attrs->latest_episode = $parsed->episode;
		} catch (Exception $e) {
			echo $e->getMessage(), "\n";
		}
	} else {
		$verbose AND printf("No new episode for %s (%d)\n", $attrs->name, $parsed->episode);
	}
}

// Write the data back out.
$dataset->save();

// Check we did some work.
if (! $did_run)
	echo 'Warning: Did not check anything. Are you sure you spelled the show name right?', "\n";