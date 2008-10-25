<?php

/**
 * arr - An array helper.
 * $Id$
 */

class arr {
	public static function maxkeylen($arr) {
		$keys = array_keys($arr);
		$max = 0;
		foreach ($keys as $key)
			(strlen($key) > $max) AND ($max = strlen($key));
		return $max;
	}
}
