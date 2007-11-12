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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
	<head>
		<meta http-equiv="Content-Type" content="application/xhtml+xml; charset=utf-8" />
		<title>HelloHella</title>
		<style type="text/css">
			<!--
			@import url('style/main.css');
			-->
		</style>
		<script src="js/prototype.js" type="text/javascript"></script>
		<script src="js/scriptaculous.js" type="text/javascript"></script>
		<script type="text/javascript">
		<!--
			function showstatus(obj) {
				if (obj.options[obj.selectedIndex].value > 1) {
					document.getElementById('sortdirection').style.display = 'inline';
				} else {
					document.getElementById('sortdirection').style.display = 'none';
				}
			}

			function confirmorder(obj) {
				var sorttype = obj.sorttype.options[obj.sorttype.selectedIndex].value;
				if (sorttype > 1) {
					return confirm('This will reorder the entire queue, continue?');
				} else {
					return true;
				}
			}

			function setstyles() {
				if (document.getElementById('hidelog') != null) {
					document.getElementById('hidelog').style.display = 'none';
				}
				if (document.getElementById('sortdirection') != null) {
					document.getElementById('sortdirection').style.display = 'none';
				}
				if (document.getElementById('log') != null) {
					document.getElementById('log').style.display = 'none';
				}
				if (document.getElementById('showlog') != null) {
					document.getElementById('showlog').style.display = 'block';
				}
			}
			
		//-->
		</script>
	</head>
	<body onload="setstyles()">
		<div id="masthead">
			<h1>HelloHella</h1>
		</div>
		<?php if (isset($errormessage)): ?>
		<div id="error">
			<?php echo $errormessage; ?>
		</div>
		<?php else: ?>
		<div id="container">
			<div id="modules">
				<h2>Add NZB Via Newzbin ID Or URL</h2>
				<div>
					<form method="post" action="<?php echo $self; ?>">
						<input type="submit" value="Go" style="float:right;" />
						<label for="nzbdownload">Article ID or URL: <input type="text" name="nzbdownload" id="nzbdownload" /></label>
					</form>
				</div>
				<h2>Set Rate Limit</h2>
				<div>
					<form method="post" action="<?php echo $self; ?>">
						<input type="submit" value="Go" style="float:right" />
						<label for="maxrate">Limit (KB/s): <input type="text" name="maxrate" id="maxrate" value="<?php echo $c->rateLimit; ?>" /></label>
					</form>
				</div>
			</div>
			<div id="current">
				<div id="commands">
					<ul>
					<?php if ($c->paused): ?>
						<li><a href="<?php echo $self; ?>?action=8" title="Resume">Resume downloading</a></li>
					<?php else: ?>
						<li><a href="<?php echo $self; ?>?action=7" title="Pause">Pause current download</a></li>
					<?php endif; ?>
						<li><a href="<?php echo $self; ?>?action=9" title="Cancel" onclick="return confirm('This will cancel the current download and remove it from the queue, continue?');">Cancel current download</a></li>
						<li><a href="<?php echo $self; ?>?action=11" title="Shut down" onclick="return confirm('HellaNZB cannot be restarted without shell access, continue?');">Shut down HellaNZB</a></li>
						<li><a href="<?php echo $self; ?>?action=10" title="Clear queue" onclick="return confirm('This cannot be undone, continue?');">Clear the Queue</a></li>
					</ul>
				</div>
				<div id="info">
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
				</div>
			</div>
			<div id="content">
				<div class="queuebox">
					Queued: <?php echo $c->queueLength; ?> items totalling <?php echo $c->queueSize; ?>MB.
				</div>
				<?php if ($c->queueLength > 0): ?>
				<form method="post" action="<?php echo $self; ?>" onsubmit="confirmorder(this)">
				<div id="scriptbox">
				<ul id="queue">
				<?php foreach ($c->queue as $queue): ?>
					<li class="queuebox" id="order_<?php echo $queue['id']; ?>">
						<div class="orderform"><select name="order[]"><?php echo $orderoptions; ?></select></div>
						<div class="queuetitle"><?php echo $queue['nzbName']; ?></div>
						<div class="queuecontrols">
							<ul>
								<li><a title="Cancel" href="<?php echo $self; ?>?action=1&amp;id=<?php echo $queue['id']; ?>"><img src="images/delete.png" alt="Cancel" /></a></li><li><a title="Up" href="<?php echo $self; ?>?action=2&amp;id=<?php echo $queue['id']; ?>"><img src="images/previous.png" alt="Up" /></a></li><li><a title="Down" href="<?php echo $self; ?>?action=3&amp;id=<?php echo $queue['id']; ?>"><img src="images/next.png" alt="Down" /></a></li><li><a title="Top" href="<?php echo $self; ?>?action=4&amp;id=<?php echo $queue['id']; ?>"><img src="images/first.png" alt="Top" /></a></li><li><a title="Bottom" href="<?php echo $self; ?>?action=5&amp;id=<?php echo $queue['id']; ?>"><img src="images/last.png" alt="Bottom" /></a></li><li><a title="Force" href="<?php echo $self; ?>?action=6&amp;id=<?php echo $queue['id']; ?>"><img src="images/force.png" alt="Force" /></a></li>
							</ul>
						</div>
						<div class="queuestats"><?php echo $queue['total_mb']; ?>MB<?php echo ($c->transferRate > 0) ? ' ETA: ' . $c->formatTimeStamp($queue['eta']) : ''; ?></div>
					</li>
				<?php endforeach; ?>
				</ul>
				<script type="text/javascript">
				<!--
				Sortable.create('queue', {
					dropOnEmpty:false,
					constraint:false,
					onUpdate: function(){
						new Ajax.Updater('scriptbox', 'index.php', {
							method:'post',
							postBody:Sortable.serialize('queue'),
							evalScripts:true,
							onComplete:function() {
								new Effect.Highlight('queue', {
									startcolor:'#660000',
									endcolor:'#999999',
									duration:0.5
								});
							}
						});
					}
				});
				if (document.getElementById('queue') != null) {
					var node = document.getElementById('queue');
					var lis = node.getElementsByTagName('li');
					for (var i = 0; i < lis.length; i ++) {
						lis[i].style.cursor = 'move';
					}
				}
				//-->
				</script>
				</div>
				<div id="controls">
					<input type="submit" name="reorder" value="Reorder Items" /> by <select name="sorttype" id="sorttype" onchange="showstatus(this);"><option value="1">Provided Order</option><option value="2">Name</option><option value="3">Size</option></select> <select name="sortdirection" id="sortdirection"><option value="1">Ascending</option><option value="2">Descending</option></select>
				</div>
				</form>
				<?php endif; ?>
				<div id="showlog"><a href="#" onclick="document.getElementById('log').style.display='block';document.getElementById('hidelog').style.display='block';document.getElementById('showlog').style.display='none';return false;">Show latest log entries</a></div>
				<div id="hidelog"><a href="#" onclick="document.getElementById('log').style.display='none';document.getElementById('hidelog').style.display='none';document.getElementById('showlog').style.display='block'; return false;">Hide log entries</a></div>
				<pre id="log"><?php foreach ($c->log as $line): echo $line . "\n"; endforeach; ?></pre>
			</div>
		</div>
		<?php endif; ?>
	</body>
</html>
