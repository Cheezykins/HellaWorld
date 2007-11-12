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

$hellainfo = array();

$hellainfo['path'] = $self;
if ($c->downloadCount > 0) {
	$hellainfo['downloading'] = htmlspecialchars($c->downloads[0]['nzbName']);
}
$hellainfo['paused'] = $c->paused;
$hellainfo['completed'] = $c->completed;
$hellainfo['size'] = $c->downloads[0]['total_mb'];
$hellainfo['remaining'] = $c->remaining;
$hellainfo['eta'] = $c->formatTimeStamp($c->eta);
$hellainfo['transferRate'] = $c->transferRate;
if ($c->processCount > 0) {
	$hellainfo['processing'] = htmlspecialchars($c->processing[0]);
}
$hellainfo['queuelength'] = $c->queueLength;
$i = 0;
$hellainfo['queue'] = array();
foreach ($c->queue as $queue) {
	$tmp = array();
	$tmp['index'] = ++$i;
	$tmp['id'] = $queue['id'];
	$tmp['nzbName'] = htmlspecialchars($queue['nzbName']);
	$tmp['totalmb'] = $queue['total_mb'];
	$tmp['eta'] = $c->formatTimeStamp($queue['eta']);
	$hellainfo['queue'][] = $tmp;
}
if ($config['showfinished']) {
	$hellainfo['finished'] = array();
	$i = 0;
	foreach($x->item as $item) {
		$tmp = array();
		$tmp['index'] = ++$i;
		$tmp['type'] = (((string)$item->type == 'SUCCESS') ? 'good' : 'bad' );
		$tmp['archiveName'] = (string)$item->archiveName;
		$tmp['destDir'] = (string)$item->destDir;
		$tmp['elapsedTime'] = (string)$item->elapsedTime;
		$tmp['finishedTime'] = date('M dS - H:i:s', (int)$item->finishedTime);
		$tmp['parMessage'] = trim((string)$item->parMessage);
		$hellainfo['finished'][] = $tmp;
	}
} else {
	$hellainfo['finished'] = false;
}
$hellainfo['log'] = $c->log;
echo json_encode($hellainfo);
?>
