<?php

/**
* Feed_Manager - Management class for feeds.
* $Id$
*/

class Feed_Manager {
	protected $dataset;
	
	public function __construct() {
		$this->dataset = new XML_Dataset('feeds.xml');
	}
	
	public function add($url) {
		// This throws an exception on error.
		nzb::add_feed($this->dataset, $url);
		
		// Write the data out, we're done here.
		$this->dataset->save();
	}
	
	public function remove($names) {
		// Support the passing of a single name.
		if (! is_array($names))
			$names = array($names);
		
		if (count($names) == 0)
			throw new Exception('Invalid parameters passed to remove.');
		
		// Make everything lower-case.
		array_map('strtolower', $names);
			
		// Keep track of if we remove any feeds.
		$removed = FALSE;
		
		// How many feeds are there?
		$feed_count = count($this->dataset->feed);
		
		// Loop over each feed, removing any that are in $names.
		for ($idx = 0; $idx < $feed_count; $idx++) {
			// Grab the feed.
			$feed = $this->dataset->feed[$idx];
			
			// Have we been asked to remove this one?
			if (in_array(strtolower($feed['name']), $names)) {
				unset($this->dataset->feed[$idx]);
				$removed = TRUE;
			}
		}
		
		// Did we do any work?
		if (! $removed)
			throw new Exception('Error: Did not match any feeds to remove.');
		
		// Write the data out, we're done for the time being.
		$this->dataset->save();
	}
	
	/**
	 * Getter/setter for latest_episode attribute.
	 */
	public function latest($name, $number = FALSE) {
		// Always use lower-case.
		$find_name = strtolower($name);
			
		// Find the feed we're interested in.
		foreach ($this->dataset->feed as $feed) {
			if (strtolower($feed['name']) == $find_name) {
				// Are we expected to set it, or just return it?
				if ((bool) $number) {
					$feed['latest_episode'] = $number;
					$this->dataset->save();
				}
				
				return $feed['latest_episode'];
			}
		}
		
		// If we fall through to here, we didn't find a match.
		throw new Exception(sprintf('Could not find feed named %s', $name));	
	}
	
	public function feeds($objects = FALSE) {
		// Initialise return array.
		$feeds = array();
		
		// Loop over each feed, add it to the array.
		foreach ($this->dataset->feed as $feed)
			if ((bool) $objects)
				$feeds[(string) $feed['name']] = $feed;
			else
				$feeds[] = $feed['name'];
		
		// Alphabetical order, please.
		if ((bool) $objects)
			ksort($feeds);
		else
			natcasesort($feeds);
		
		return $feeds;
	}
}