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
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
	<head>
		<meta http-equiv="Content-Type" content="application/xhtml+xml; charset=utf-8" />
		<title>HellaWorld v<?php echo $hellaworldversion; ?></title>
		<link rel="stylesheet" type="text/css" href="style/main.css" />
		<link rel="stylesheet" type="text/css" href="style/nonjs.css" />
		<link rel="stylesheet" type="text/css" href="style/thickbox.css" />
		<link rel="stylesheet" type="text/css" href="style/tabs.css" />
		<link rel="icon" href="images/trac.ico" type="image/x-icon" />
		<link rel="shortcut icon" href="images/trac.ico" type="image/x-icon" />
		<!--[if lte IE 7]>
		<link rel="stylesheet" href="style/tabs-ie.css" type="text/css" />
		<![endif]-->
		<script src="js/jquery.js" type="text/javascript"></script>
		<script src="js/thickbox.js" type="text/javascript"></script>
		<script src="js/interface.js" type="text/javascript"></script>
		<script src="js/tabs.js" type="text/javascript"></script>
		<script src="js/hellaworld.php" type="text/javascript"></script>
	</head>
	<body onload="init();">
		<div id="masthead">
			<h1>HellaWorld</h1>
			<div>
				<p><?php printf(_('HellaNZB %s - Uptime: %s'), $c->version, $c->uptime); ?></p>
				<p><?php printf(_('Downloaded: %s files in %s segments totalling %sMB via %s NZB files'), $c->totalFiles, $c->totalSegments, $c->totalMB, $c->totalNZBs); ?></p>
			</div>
		</div>
		<div id="container">
			<div id="modules">
				<h2><?php echo _('Add NZB Via Newzbin ID Or URL'); ?></h2>
				<div id="nzbinput">
					<form method="get" action="<?php echo $self; ?>">
						<fieldset>
							<label for="nzbdownload"><?php echo _('Article ID or URL:'); ?><br /> <input type="text" name="nzbdownload" id="nzbdownload" /></label>
							<input type="submit" value="Go" style="float:right;" />
						</fieldset>
					</form>
					<form method="post" enctype="multipart/form-data" action="<?php echo $self; ?>">
						<fieldset>
							<label for="nzbupload" ><?php echo _('Upload NZB:'); ?><br/> <input size=27 type="file" name="nzbupload" id="nzbupload" /></label>
							<input type="submit" value="Go" style="float:right;" />
						</fieldset>
					</form>
				</div>
				<h2><?php echo _('Set Rate Limit'); ?></h2>
				<div id="downrate" >
					<form method="get" action="<?php echo $self; ?>">
						<fieldset>
							<input type="submit" value="Go" style="float:right" />
							<label for="maxrate"><?php echo _('Limit (KB/s):'); ?> <input type="text" name="maxrate" id="maxrate" value="<?php echo $c->rateLimit; ?>" /></label>
						</fieldset>
					</form>
				</div>
			</div>
			<div id="current">
				<div id="commands">
					<ul>
					<?php if ($c->paused): ?>
						<li><a href="<?php echo $self; ?>?action=8" title="<?php echo _('Resume downloading'); ?>"><?php echo _('Resume downloading'); ?></a></li>
					<?php else: ?>
						<li><a href="<?php echo $self; ?>?action=7" title="<?php echo _('Pause current download'); ?>"><?php echo _('Pause current download'); ?></a></li>
					<?php endif; ?>
						<li><a href="<?php echo $self; ?>?action=9" title="<?php echo _('Cancel current download'); ?>" onclick="return confirm('<?php echo _('This will cancel the current download and remove it from the queue, continue?'); ?>');"><?php echo _('Cancel current download'); ?></a></li>
						<li><a href="<?php echo $self; ?>?action=11" title="<?php echo _('Shut down HellaNZB'); ?>" onclick="return confirm('<?php echo _('HellaNZB cannot be restarted without shell access, continue?'); ?>');"><?php echo _('Shut down HellaNZB'); ?></a></li>
						<li><a href="<?php echo $self; ?>?action=10" title="<?php echo _('Clear the queue'); ?>" onclick="return confirm('<?php echo _('This cannot be undone, continue?'); ?>');"><?php echo _('Clear the queue'); ?></a></li>
					</ul>
				</div>
				<div id="info">
<h2><?php echo _('Currently Downloading'); ?></h2>
<?php if ($c->downloadCount > 0): foreach($c->downloads as $download): ?>
<div class="nzbname"><?php echo htmlspecialchars($download['nzbName']); ?></div>
<div class="queuestats"><img src="progress.php?percentage=<?php echo $c->completed; ?>" alt="progress bar" /> <?php printf(_('%s%% complete'), $c->completed); ?></div>
<div class="queuestats"><?php printf(_('%sMB of %sMB remaining'), $c->remaining, $download['total_mb']); ?></div>
<div class="queuestats" style="margin-bottom: 5px;"><?php if (!$c->paused): printf(_('ETA %s at %sKB/s'), $c->formatTimeStamp($c->eta), $c->transferRate); else: echo _('PAUSED'); endif; ?></div>
<?php endforeach; else: ?>
<div style="margin-bottom: 5px;"><?php echo _('Nothing'); ?></div>
<?php endif; ?>
<h2><?php echo _('Currently Processing'); ?></h2>
<?php if ($c->processCount > 0): foreach($c->processing as $processing): ?>
<div><?php echo htmlspecialchars($processing); ?></div>
<?php endforeach; else: ?>
<div><?php echo _('Nothing'); ?></div>
<?php endif; ?>
				</div>
			</div>
			<div id="content">
				<div id="queuestatus" class="queuebox first">
					<?php printf(_('Queued: %s items totalling %sMB'), $c->queueLength, $c->queueSize); ?>
				</div>
				<form method="post" action="<?php echo $self; ?>" onsubmit="confirmorder(this)">
<ul id="queue">
<?php if ($c->queueLength == 0): ?>
	<li class="queuebox" style="text-align: center; font-weight: bold;"><?php echo _('Queue is empty'); ?></li>
<?php endif; ?>
<?php $i = 0; foreach ($c->queue as $queue): $i++ ?>
	<li class="queuebox" id="order_<?php echo $queue['id']; ?>">
		<div class="orderform"><input type="text" name="order[]" onchange="stopRefresher();" value="<?php echo $i; ?>" /></div>
		<div class="queuetitle"><?php echo htmlspecialchars($queue['nzbName']); ?></div>
		<ul class="queuecontrols">
			<li class="handle"><?php echo _('Drag me'); ?></li><li class="control"><a href="index.php?info=<?php echo $i; ?>&amp;KeepThis=true&amp;TB_iframe=true&amp;height=200&amp;width=500" class="thickbox"><img src="images/information.png" alt="Info" /></a></li><li class="control"><a title="Cancel" href="<?php echo $self; ?>?action=1&amp;id=<?php echo $queue['id']; ?>"><img src="images/delete.png" alt="Cancel" /></a></li><li class="control"><a title="Up" href="<?php echo $self; ?>?action=2&amp;id=<?php echo $queue['id']; ?>"><img src="images/up.png" alt="Up" /></a></li><li class="control"><a title="Down" href="<?php echo $self; ?>?action=3&amp;id=<?php echo $queue['id']; ?>"><img src="images/down.png" alt="Down" /></a></li><li class="control"><a title="Top" href="<?php echo $self; ?>?action=4&amp;id=<?php echo $queue['id']; ?>"><img src="images/top.png" alt="Top" /></a></li><li class="control"><a title="Bottom" href="<?php echo $self; ?>?action=5&amp;id=<?php echo $queue['id']; ?>"><img src="images/bottom.png" alt="Bottom" /></a></li><li class="control"><a title="Force" href="<?php echo $self; ?>?action=6&amp;id=<?php echo $queue['id']; ?>"><img src="images/force.png" alt="Force" /></a></li>
		</ul>
		<div class="queuestats"><?php printf(_('%sMB'), $queue['total_mb']); if ($c->transferRate > 0): printf(_(' ETA: %s'), $c->formatTimeStamp($queue['eta'])); endif; ?></div>
	</li>
<?php endforeach; ?>
</ul>
				<div id="controls">
					<span id="refresher">
					<input type="checkbox" name="refresh" id="refresh" onclick="if (this.checked) { createCookie('HHRefresh', 1, 30);startRefresher(); } else { createCookie('HHRefresh', 0, 30);stopRefresher(); }" /> <label for="refresh" class="refresher"><?php echo _('Refresh every 15 seconds'); ?></label> <input type="button" value="<?php echo _('Refresh Now'); ?>" onclick="refreshNow();" />
					</span>
					<input type="submit" name="reorder" value="<?php echo _('Reorder Items'); ?>" /> <?php echo _('by'); ?> <select name="sorttype" id="sorttype" onchange="showstatus(this);"><option value="1"><?php echo _('Provided Order'); ?></option><option value="2"><?php echo _('Name'); ?></option><option value="3"><?php echo _('Size'); ?></option></select> <select name="sortdirection" id="sortdirection"><option value="1"><?php echo _('Ascending'); ?></option><option value="2"><?php echo _('Descending'); ?></option></select>
				</div>
				</form>
		</div>
	</div>
<div id="tabcontainer">
	<ul>
		<li><a accesskey="1" href="#fragment-1"><span><?php echo _('Log Entries'); ?></span></a></li>
		<?php if (isset($config['showfinished']) && $config['showfinished']): ?><li><a accesskey="2" href="#fragment-2"><span><?php echo _('Finished Items'); ?></span></a></li><?php endif; ?>
		<li><a accesskey="3" href="#fragment-3"><span><?php echo _('Bookmarklet'); ?></span></a></li>
	</ul>
<div id="fragment-1">
<div id="log"><?php foreach ($c->log as $line): echo $line . "<br />"; endforeach; ?></div>
</div>
<?php if (isset($config['showfinished']) && $config['showfinished']): $finishedcount = count($x->item);
	$baseurl = (!strstr($config['basepath'], '://') ? $protocol . '://' . htmlentities($_SERVER['HTTP_HOST']) : '').$config['basepath'].'/';
?>
<div id="fragment-2">
<div id="finishedbox">
	<form action="<?php echo $self; ?>" method="post">
		<div id="finishedcontrols">
			<span id="finishedcount"><?php printf(_('Finished items: %s'), $finishedcount); ?></span>
			<?php if ($finishedcount > 0): ?>
			<input type="submit" name="clearnzbs" value="<?php echo _('Clear Finished NZBs'); ?>" />
			<?php endif; ?>
		</div>
	</form>
	<ul id="finished">
		<?php if ($finishedcount == 0): ?>
			<li class="queuebox" style="text-align: center; font-weight: bold;"><?php echo _('No Finished Items'); ?></li>
		<?php else: ?>
			<?php $i = 0; foreach($x->item as $item):
				$i++; $durl = '';
				if (!strncmp($config['destpath'], (string)$item->destDir, strlen($config['destpath'])) && is_dir((string)$item->destDir)):
					$durl = $baseurl.substr((string)$item->destDir, strlen($config['destpath'])+1);
				endif
			?>
			<li class="queuebox <?php echo (((string)$item->type == 'SUCCESS') ? 'good' : 'bad'); ?>">
				<div>
					<?php if ($config['showlinks'] && !empty($durl)): ?>
					<div><a class="queuetitle" href="<?php echo $durl; ?>"><?php echo (string)$item->archiveName; ?></a></div>
					<?php else: ?>
					<div class="queuetitle"><?php echo (string)$item->archiveName; ?></div>
					<?php endif ?>
				</div>
				<ul class="queuecontrols">
					<li class="control"><a href="<?php echo $self; ?>?removefinished=<?php echo $i; ?>"><?php echo _('Remove'); ?></a></li>
				</ul>
				<div class="queuestats"><?php printf(_('Finished on: %s Processing Time: %s'), date('M dS - H:i:s', (int)$item->finishedTime), (string)$item->elapsedTime); if (trim((string)$item->parMessage) != ''): printf(_(' Par message: %s'), (string)$item->parMessage); endif; ?></div>
			</li>
			<?php endforeach; ?>
		<?php endif; ?>
	</ul>
</div>
</div>
		<?php endif; ?>
<div id="fragment-3">
<h2><?php echo _('HellaWorld Bookmarklet'); ?></h2>
<p class="bkmark"><?php echo _("Right click on this link and bookmark it, or drag it to your bookmarks/favorites to create a Newzbin shortcut. Clicking on this shortcut when on a Newzbin article will add the NZB to HellaNZB's queue."); ?></p>
<p class="bkmark"><a href="javascript:c=location.href;if(c.match(/browse\/post\/\d+/)){location.href='<?php echo $protocol . '://' . htmlentities($_SERVER['HTTP_HOST']) . $self ?>?bookmarklet='+encodeURIComponent(c);}else{void(0);}"><?php echo _('Send to HellaWorld'); ?></a></p>
</div>
</div>
	</body>
</html>
