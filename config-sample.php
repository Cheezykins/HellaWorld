<?php

// Change these settings to match your desired configuration.

// The default settings correspond to an unaltered installation
// of HellaNZB running locally.

	$config = array(
		'host'			=>	'localhost',	// The address HellaNZB is running on.
		'port'			=>	'8760',			// The port HellaNZB is listening on.
		'auth'			=>	'open',			// Can be open, closed, exclusive or hybrid, see readme for details

		// username and password required for open and hybrid authentication
		'username'		=>	'hellanzb',		// This is usually hardcoded as hellanzb and shouldnt need changing
		'password'		=>	'changeme',		// The password specified in hellanzb.conf

		'showfinished'	=>	true,			// Show finished items, see README for details
		'language'		=>	'en_GB',		// The language code HellaWorld should use

		// this is a range of allowed IP addresses for hybrid and exclusive authentication,
		// address ranges must be comma separated, use xxx.xxx.xxx.xxx/32 to limit it to a single
		// address, where xxx.xxx.xxx.xxx is the address you wish to limit it to.
		'iprange'		=>	'192.168.0.0/16,10.0.0.0/8,172.16.0.0/12',

		'showlinks'		=> false,			// Display direct links to downloaded files
		'basepath'		=> '/media',		// The URL to the Apache alias to the downloads
		'destpath'		=> '/mnt/usenet',	// The actual file path
	);

// $Id$
?>
