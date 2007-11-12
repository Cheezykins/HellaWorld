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
<h2>Currently Downloading</h2>
<?php if ($c->downloadCount > 0): ?>
<div><?php echo $c->downloads[0]['nzbName']; ?></div>
<div class="queuestats"><img src="progress.php?percentage=<?php echo $c->completed; ?>" alt="progress bar" /> <?php echo $c->completed; ?>% complete</div>
<div class="queuestats"><?php echo $c->remaining; ?>MB of <?php echo $c->downloads[0]['total_mb']; ?>MB remaining</div>
<div class="queuestats" style="margin-bottom: 5px;"><?php echo ((!$c->paused) ? 'ETA ' . $c->formatTimeStamp($c->eta) . ' at ' . $c->transferRate . 'KB/s' : 'PAUSED'); ?></div>
<?php else: ?>
<div style="margin-bottom: 5px;">Nothing</div>
<?php endif; ?>
<h2>Currently Processing</h2>
<?php if ($c->processCount > 0): ?>
<div><?php echo $c->processing[0]; ?></div>
<?php else: ?>
<div>Nothing</div>
<?php endif; ?>
