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

// Loop over each feed, parse it, see if the latest episode has changed.
foreach ($dataset->feed as $feed) {
	$attrs =& $feed->attributes();
	
	$atom = new Atom_Feed($attrs->url);
	$latest = $atom->entry[0];
	$parsed = nzb::parse($latest->title);
	
	if ($parsed->episode > $attrs->latest_episode) {
		// There's a new ep!!!
		echo sprintf('New episode of %s (%s > %s)',
				$attrs->name, $parsed->episode, $attrs->latest_episode), "\n";
		$nzb_id = end(explode('/', substr($latest->id, 0, -1)));
		
		try {
			$controller->enqueueNewzbin($nzb_id);
			$attrs->latest_episode = $parsed->episode;
		} catch (Exception $e) {
			echo $e->getMessage(), "\n";
		}
	} else {
//		echo 'Same old. '.$parsed->episode, "\n";
	}
}

$dataset->save();