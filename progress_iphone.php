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

$Id: progress.php 149 2007-10-16 15:59:38Z chris $
*/

	$width = 235;
	$height = 6;

	if (array_key_exists('percentage', $_GET) && preg_match('/^\d+$/', $_GET['percentage'])) {
		$percentage = $_GET['percentage'];
	} else {
		$percentage = 0;
	}

	$im = imagecreate($width, $height);
	$bg = imagecolorallocate($im, 200, 200, 200);
	$outline = imagecolorallocate($im, 0, 150, 255);
	$fill = imagecolorallocate($im, 255, 255, 255);

	imagefilledrectangle($im, 1, 1, $width - 2, $height - 2, $outline);

	if ($percentage < 100) {
		imagefilledrectangle($im, round((($width - 2) * ($percentage / 100)) + 1), 1, $width - 2, $height - 2, $fill);
	}
	header('content-type: image/png');
	imagepng($im);
?>
