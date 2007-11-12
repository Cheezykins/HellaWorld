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
	  HelloHella nor the names of its contributors may be used to
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
*/
?>
hellainfo = {
	"path" : "<?php echo $self; ?>",
<?php if ($c->downloadCount > 0): ?>
	"downloading" : "<?php echo $c->downloads[0]['nzbName']; ?>",
<?php else: ?>
	"downloading" : null,
<?php endif; ?>
<?php if ($c->paused): ?>
	"paused" : true,
<?php else: ?>
	"paused" : false,
<?php endif; ?>
	"completed" : <?php echo $c->completed; ?>,
	"size" : <?php echo $c->downloads[0]['total_mb']; ?>,
	"remaining" : <?php echo $c->remaining; ?>,
	"eta" : "<?php echo $c->formatTimeStamp($c->eta); ?>",
	"transferRate" : <?php echo $c->transferRate; ?>,
<?php if ($c->processCount > 0): ?>
	"processing" : "<?php echo $c->processing[0]; ?>",
<?php else: ?>
	"processing" : null,
<?php endif; ?>
<?php if ($c->queueLength > 0): ?>
	"queuelength" : <?php echo $c->queueLength; ?>,
<?php else: ?>
	"queuelength" : 0,
<?php endif; ?>
	"queue" : [
<?php $i = 0; foreach ($c->queue as $queue): $i ++ ?>
		{
			"index" : <?php echo $i; ?>,
			"id" : <?php echo $queue['id']; ?>,
			"nzbName" : "<?php echo $queue['nzbName']; ?>",
			"totalmb" : <?php echo $queue['total_mb']; ?>,
			"eta" : "<?php echo $c->formatTimeStamp($queue['eta']); ?>"
		},
<?php endforeach; ?>
	],
	"log" : [<?php foreach($c->log as $line): echo '"' . $line . '",'; endforeach; ?>]
}
