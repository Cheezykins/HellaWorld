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
}
