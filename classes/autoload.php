<?php

/**
 * Autoload definition.
 * $Id$
 */

function __autoload($class_name) {
	if ($class_name != 'HellaController')
		$class_name = str_replace('_', '-', strtolower($class_name));
	
	if (file_exists($path = APPPATH.'classes/'.$class_name.'.php'))
		require_once($path);
}
