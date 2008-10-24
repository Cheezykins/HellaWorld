<?php

/**
* Newzbin Helper
* $Id$
*/
class nzb {
	public static function parse($title) {
		if (! preg_match('/(.*?) - ([0-9]+)x([0-9]+) - (.*?)$/', $title, $matches))
			throw new Exception('Failed to parse '.$title);
		
		return (object) array(
			'show' => $matches[1],
			'season' => $matches[2],
			'episode' => $matches[3],
			'episode_name' => $matches[4],
		);
	}
	
	public static function add_feed($dataset, $url) {
		// Check we don't already have this feed...
		foreach ($dataset->feed as $feed)
			if ($feed['url'] == $url)
				throw new Exception('Feed already exists.');
			
		// Add the new feed!
		$atom = new Atom_Feed($url);
		$latest = reset($atom->entry);
		$parsed = nzb::parse($latest);
		
		$feed = $dataset->addNode('feed', array('url' => $url));
		$feed->addAttribute('name', $parsed->show);
		$feed->addAttribute('latest_episode', $parsed->episode);
	}
}
