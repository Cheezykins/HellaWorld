<?php

/**
* Atom_Feed - Very basic atom feed helper.
* $Id$
*/
class Atom_Feed {
	protected $feed;
	
	function __construct($url) {
		$this->feed = simplexml_load_string(file_get_contents($url));
	}
	
	function __get($key) {
		return $this->feed->$key;
	}
}
