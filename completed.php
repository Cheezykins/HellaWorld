#!/usr/local/bin/php
<?php

/*
Copyright (c) 2007, Chris Stretton
All rights reserved.

Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions
are met:

    * Redistributions of source code must retain the above copyright
      notice, this list of conditions and the following disclaimer.

    * Redistributions in binary form must reproduce the above
      copyright notice, this list of conditions and the following
      disclaimer in the documentation and/or other materials provided
      with the distribution.

    * Neither the names of The Cheezy Blog, Cheezyblog.net or
      HellaWorld nor the names of its contributors may be used to
      endorse or promote products derived from this software without
      specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
"AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION)
HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT,
STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED
OF THE POSSIBILITY OF SUCH DAMAGE.

$Id$

*/

	$path = htmlspecialchars(dirname($_SERVER['PHP_SELF'])) . "/";
	try {
		if (!file_exists($path . 'completed.xml')) {
			throw new Exception('completed.xml does not exist.');
		}
		$xmlstr = file_get_contents($path . 'completed.xml');
		$x = @new SimpleXMLElement($xmlstr);
		$e = $x->addChild('item');
		$e->addChild('type', $_SERVER['argv'][1]);
		$e->addChild('archiveName', $_SERVER['argv'][2]);
		$e->addChild('destDir', $_SERVER['argv'][3]);
		$e->addChild('elapsedTime', $_SERVER['argv'][4]);
		$e->addChild('finishedTime', time());
		$e->addChild('parMessage', $_SERVER['argv'][5]);
		$fp = fopen($path . 'completed.xml', 'w');
		if (!$fp) {
			throw new Exception('Unable to write to ' . $path . 'completed.xml, please check permissions.');
		}
		fwrite($fp, $x->asXML());
		fclose($fp);
	} catch (Exception $e) {
		die('Error: ' . $e->getMessage() . "\n");
	}

?>
