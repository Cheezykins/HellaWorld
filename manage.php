<?php

/**
 * Command-line feed management script.
 * $Id$
 */

// Remove the manage.php arg.
array_shift($_SERVER['argv']);

// Make all args lower-case.
$args = array_map('strtolower', $_SERVER['argv']);

// Do we want verbosity?
$verbose = in_array('-v', $args);

// Remove the -v arg if present.
if ($verbose)
	unset($args[array_search('-v', $args)]);

// Give the user some help.
if (in_array('--help', $args)) {
	die("Usage:\n\tphp manage.php remove <show name> [show name] ...\n"
		."OR\n"
		."\tphp manage.php add <url>\n"
		."OR\n"
		."\tphp manage.php list\n"
		."OR\n"
		."\tphp manage.php latest <show name> [episode number]"
		."OR\n"
		."\tphp manage.php [-v] cron [show name] [show name]");
}

require_once('classes/autoload.php');
require_once('config.php');
$controller = new HellaController($config['host'], $config['port'], $config['username'], $config['password']);

$manager = new Feed_Manager();

// Grab the command off the args.
$command = array_shift($args);

// What are we supposed to do?
switch ($command) {
	// Add a new feed by url.
	case 'add':
		// Sanity check.
		if (count($args) != 1)
			die("Invalid arguments passed for \'add\'.\n");
		
		// Do the work.
		$manager->add($args[0]);
		break;
	
	// Remove the feed(s) that they pass by name.
	case 'remove':
		$manager->remove($args);
		break;
	
	// Output the list of feeds we have.
	case 'list':
		$feeds = $manager->feeds();
		$unit = (count($feeds) == 1) ? 'feed' : 'feeds';
		
		echo sprintf('%d %s:', count($feeds), $unit), "\n";
		foreach ($feeds as $feed)
			echo "\t", $feed, "\n";
		break;
	
	// Get or set the latest_episode attribute.
	case 'latest':
		// How many args do we have?
		$arg_count = count($args);
		
		try {
			if ($arg_count == 1)
				echo sprintf('Latest episode for %s is %d',
					$args[0], $manager->latest($args[0])), "\n";
			elseif ($arg_count == 2)
				echo sprintf('Latest episode for %s set to %d',
					$args[0], $manager->latest($args[0], $args[1])), "\n";
			else
				throw new Exception('Invalid parameters passed to \'latest\'.');		
		} catch (Exception $e) {
			die($e->getMessage."\n");			
		}
		break;
	
	// Run the cron task to update all feeds.
	case 'update':
		// Say something for the log...
		printf('[%s] Update started.'."\n", date('d/m/Y H:i:s'));
		
		// Keep note of if we did anything or not.
		$did_run = FALSE;
		
		// Loop over each feed, parse it, see if the latest episode has changed.
		foreach ($manager->feeds(TRUE) as $feed) {
			// Only fetch the ones specified, if any.
			if ((bool) count($args)) {
				if (! in_array(strtolower($feed['name']), $args)) {
					$verbose AND printf("Skipping %s\n", $feed['name']);
					continue;
				};
			};

			// Make a note that we did some work.
			$did_run = TRUE;

			// Fetch the data from newzbin.
			$verbose AND printf("Fetching feed for %s... ", $feed['name']);
			$atom = new Atom_Feed($feed['url']);
			$latest = $atom->entry[0];
			$parsed = nzb::parse($latest->title);

			// Is the 1st item newer than the latest we recorded?
			if ($parsed->episode > $feed['latest_episode']) {
				((bool) $verbose)
					? printf('Queueing new episode! (%d)'."\n", $parsed->episode)
					: printf('Queueing new episode of %s (%d)'."\n", $feed['name'], $parsed->episode);

				// Grab the nzb id off the url.
				$nzb_id = end(explode('/', substr($latest->id, 0, -1)));

				try {
					$controller->enqueueNewzbin($nzb_id);
					$manager->latest($feed['name'], $parsed->episode);
				} catch (Exception $e) {
					echo $e->getMessage(), "\n";
				}
			} else {
				$verbose AND printf("No new episode (%d)\n", $parsed->episode);
			}
		}
		
		if (! $did_run)
			echo "Warning: Did not match any feeds to update.\n";
			
		printf('[%s] Update finished.'."\n", date('d/m/Y H:i:s'));
	
		break;
	
	// Unknown command. Complain.
	default:
		die("Invalid command: $command\n");
}