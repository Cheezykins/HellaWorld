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

$Id: default.php 157 2007-11-02 19:54:40Z chris $

*/

function downloadingDetails($downloading){
	global $c;
	$out = "<span id='downloadingName'>".$downloading["nzbName"]."</span>";
	$out .="<div id='progressbar' class='legend'><img src='progress_iphone.php?percentage=".$c->completed."' alt='progress bar' /> ".$c->completed."% completed</div>";
	$out .="<div id='downloadedAmount' class='legend'>".$c->remaining."MB of ".$downloading['total_mb']."MB remaining</div>";
	$out .="<div id='etaContainer' class='legend'>";
		if (!$c->paused): 
			$out.="ETA <span id='eta'>".$c->formatTimeStamp($c->eta)."</span>, at <span id='transferRate'>".$c->transferRate."</span>KB/s";
		else:
			$out .="PAUSED";
		endif;	
	$out .="</div>";
	return $out;
}
function processingDetails($processing){
	global $c;
	return htmlspecialchars($processing);
}
$queueOptions="";
function queueDetails($queue,$i=0){
	global $c, $queueOptions;
	
	$out="";
	if(!$i){
		$out .= "<a href='#queueOptions_$i'><span class='label'>"._('Queue')." : </span>"; 
	} else {
		$out .="<span class='drag' id='order_".$i."'>Drag</span><a href='#queueOptions_$i'>";
	}
	$out .= $queue["nzbName"];
	$out.="<div class='legend'>Size : ".$queue['total_mb']."MB</div>";

	$queueOptions.="<ul id='queueOptions_$i' title='".$queue["nzbName"]."'>";
	$queueOptions.="<li><span class='label'>Queue Item : </span> ".$queue["nzbName"]."</li>";

	if ($c->transferRate > 0):
		$out .="<div class='legend'> ETA : ".$c->formatTimeStamp($queue['eta'])."</div>";
		$queueOptions .="<li> ETA : ".$c->formatTimeStamp($queue['eta'])."</li>";
	endif;

	$queueOptions.="<li>Size : ".$queue['total_mb']."MB</li>";
	$queueOptions.="<li class='control information'><a href='iphone.php?info=".$i."; ?>&amp;KeepThis=true&amp;TB_iframe=true'>Enter details</a></li>";
	$queueOptions.="<li class='control delete'><a title='Cancel' href='".$self."?action=1&amp;id=".$queue['id']."'>Delete </a></li>";
	$queueOptions.="<li class='control force'><a title='Force' href='".$self."?action=6&amp;id=".$queue['id']."'>Force Download</a></li>";


	$queueOptions.="</ul>";



	$out.="</a>";
	return $out;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
	<head>
		<meta http-equiv="Content-Type" content="application/xhtml+xml; charset=utf-8" />
		<title>HellaWorld v<?php echo $hellaworldversion; ?></title>
		<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;"/>
		<script src="js/jquery.min.js" type="text/javascript"></script>
		<script src="js/jquery-ui-packed" type="text/javascript"></script>
		<script src="js/thickbox.js" type="text/javascript"></script>
		<script src="js/interface.js" type="text/javascript"></script>
		<script src="js/tabs.js" type="text/javascript"></script>
		<style type="text/css" media="screen">@import "iui/iui.css";</style>
		<style type="text/css" media="screen">@import "style/iphone.css";</style>
		<script type="application/x-javascript" src="iui/iui.js"></script>
	</head>
	<body>
		<div id="masthead" class="toolbar">
			<h1 id="pageTitle">HellaNZB</h1>
			<a id="backButton" class="button" href="#">Back</a>
			<a href="#addNZBdialog" class="button"><strong>&nbsp;+&nbsp;</strong></a>
		</div>
		
		
		<ul id="Home" title="HellaWorld" selected="true">
			<li class="group">Downloads</li>
			<li><span class="label"><?=_('Downloading');?> : </span>
				<?php
				if ($c->downloadCount > 0):
					if ($c->downloadCount == 1):
						echo downloadingDetails($c->downloads[0]);
					 else:
						echo "<a href='#downloadingDetails'>".$c->donloadCount." items</a>";
					endif;
				else:
					echo _('Nothing');
				endif;
				?>
			</li>
			
			<li><span class="label"><?=_('Processing');?> : </span>
			<?php
			if ($c->processCount > 0): //Currently Processing
				if($c->processCount==1):
					echo processingDetails($c->processing[0]);
				else:
					echo "<a href='#processingDetails'>".$c->processCount."items</a>";
 				endif;
			else:
				echo _('Nothing');
			endif;
			?>
			</li>

			<li>
			<?php
			if ($c->queueLength > 0):
				if ($c->queueLength == 1):
					echo queueDetails($c->queue[0]);
				else:
					echo "<a href='#downloadQueue'><span class='label'>Queue : </span>".$c->queueLength." items</a>";
				endif;
			else : 
				echo "<span class='label'>Queued : </span>"._('Nothing');
			endif;
			?>

			<li class="group">Control</li>
			
			<?php if ($c->downloadCount > 0): ?>
				<?php if ($c->paused): ?>
					<li class='resume'><a type="cancel" type="cancel" href="<?php echo $self; ?>?action=8" title="<?php echo _('Resume downloading'); ?>"><?php echo _('Resume downloading'); ?></a></li>
				<?php else: ?>
					<li class='pause'><a type="cancel" type="cancel" href="<?php echo $self; ?>?action=7" title="<?php echo _('Pause current download'); ?>"><?php echo _('Pause current download'); ?></a></li>
				<?php endif; ?>
				<li class='cancel'><a type="cancel" href="<?php echo $self; ?>?action=9" title="<?php echo _('Cancel current download'); ?>" onclick="return confirm('<?php echo _('This will cancel the current download and remove it from the queue, continue?'); ?>');"><?php echo _('Cancel current download'); ?></a></li>
			<?php endif; ?>

			<li class='stop'><a type="cancel" href="<?php echo $self; ?>?action=11" title="<?php echo _('Shut down HellaNZB'); ?>" onclick="return confirm('<?php echo _('HellaNZB cannot be restarted without shell access, continue?'); ?>');"><?php echo _('Shut down HellaNZB'); ?></a></li>
			<li class='clear'><a type="cancel" href="<?php echo $self; ?>?action=10" title="<?php echo _('Clear the queue'); ?>" onclick="return confirm('<?php echo _('This cannot be undone, continue?'); ?>');"><?php echo _('Clear the queue'); ?></a></li>


			<li class="group">HellaNZB Configuration</li>
			<li><a href="#systemInformation" target="_self">System Information</a></li>
			<li><a href="/Application_HellaPhone" target="_self">HellaPhone</a></li>
			<li class='add'><a href="#addNZBPanel" target="_self">Add NZB</a></li>
			<li><a href="#limitRate" target="_self">Limit Rate</a></li>
			

			<li class="group">History</li>
			<li><a href="#logEntries" target="_self">Log Entries</a></li>
			<?php if (isset($config['showfinished']) && $config['showfinished']): ?>
				<li><a href="#finishedItems" target="_self">Finished Items</a></li>
			<?php endif;?>

		</ul>

		<?php
			//Multiple downloading items
			if($c->downloadCount>1){
				echo "<ul id='downloadingDetails' title='Downloading Items'>";
				foreach($c->downloads as $downloading){
					echo "<li>".downloadingDetails($downloading)."</li>";
				}
				echo "</ul>";
			}

			//Multiple processing items
			if($c->processCount>1){
				echo "<ul id='processingDetails' title='Processing Items'>";
				foreach($c->queue as $queue){
					echo "<li>".processingDetails($queue)."</li>";
				}
				echo "</ul>";
			}
			
			//Multiple Queue Items
			if($c->queueLength>1){
				echo "<ul id='downloadQueue' title='Queued Items'>";
				$i=0; foreach($c->queue as $queue){
					$i++;
					echo "<li class='drag' id='queue-order_{$queue['id']}'>".queueDetails($queue, $i)."</li>";
				}
				echo "</ul>";
			}
			echo $queueOptions;
		?>



		<ul id="systemInformation" title="System Information">
			<li class="group">System</li>
			<li><span class="label">HellaNZB Version :</span> <?=$c->version?></li>
			<li><span class="label">Uptime :</span><span id="info_uptime"><?=$c->uptime?></span></li>
			<li class="group">Transfer</li>
			<li><span id="info_totalfiles"><?=$c->totalFiles?></span><span class="label"> Files Downloaded</span> </li>
			<li><span id="info_totalsegments"><?=$c->totalSegments?></span><span class="label"> Segments</span> </li>
			<li><span id="info_totalmb"><?=$c->totalMB?></span><span class="label"> MBs transferred</span></li>
			<li><span id="info_totalnzbs"><?=$c->totalNZBs?></span><span class="label"> downloaded NZBs</span></li>
		</ul>

		<form id="addNZBPanel" class="panel" action="<?php echo $self; ?>"  method="get">
			<h2><?php echo _('Add NZB Via Newzbin ID Or URL'); ?></h2>
			<fieldset>
				<div class="row">
					<label for="nzbdownload"><?php echo _('ID or URL'); ?></label>
					<input type="text" name="nzbdownload" id="nzbdownload" />
				</div>
			</fieldset>
		</form>
		<form id="addNZBdialog" class="dialog" action="<?php echo $self; ?>" method="get">
			<fieldset>
				<h1><?php echo _('Add NZB Via Newzbin ID Or URL'); ?></h1>
	            <a class="button leftButton" type="cancel">Cancel</a>
	            <a class="button blueButton" type="submit">Add NZB</a>
	
				<label for="nzbdownload"><?php echo _('Article ID or URL:'); ?></label>
				<input type="text" name="nzbdownload" id="nzbdownload" />
			</fieldset>
		</form>

		<form method="get" id="limitRate" class="panel" action="<?php echo $self; ?>">
			<h2><?php echo _('Set Rate Limit'); ?></h2>
			<fieldset>
				<div class="row">
					<label for="maxrate"><?php echo _('Limit (KB/s):'); ?></label>
					<input type="text" name="maxrate" id="maxrate" value="<?php echo $c->rateLimit; ?>" />
				</div>
			</fieldset>
		</form>
		
		<ul id="logEntries">
			<?php foreach ($c->log as $line): echo "<li>".$line . "</li>"; endforeach; ?>
		</ul>

		<?php if (isset($config['showfinished']) && $config['showfinished']): $finishedcount = count($x->item); ?>
		<ul id="finishedItems">
			<li><?php printf(_('Finished items: %s'), $finishedcount); ?></li>
			<?php if ($finishedcount > 0): ?>
				<li>
				<form action="<?php echo $self; ?>" method="post" id="finishedControls" class="dialog">
					<fieldset>
							<input type="submit" name="clearnzbs" value="<?php echo _('Clear Finished NZBs'); ?>" />
					</fieldset>
				</form>
			</li>
			<?php endif; ?>
			
			<?php if ($finishedcount == 0): ?>
				<li class="queuebox"><?php echo _('No Finished Items'); ?></li>
			<?php else: ?>
				<?php $i = 0; foreach($x->item as $item): $i++; ?>
				<li class="queuebox <?php echo (((string)$item->type == 'SUCCESS') ? 'good' : 'bad'); ?>">
						<?php echo (string)$item->archiveName; ?>
				</li>
				<li class="group">Queue Control</li>
				<li><a href="<?php echo $self; ?>?removefinished=<?php echo $i; ?>"><?php echo _('Remove'); ?></a></li>
				<li class="group">Queue Stats</li>
				<li class="queuestats">
					<?php printf(_('Finished on: %s Processing Time: %s'), date('M dS - H:i:s', (int)$item->finishedTime), (string)$item->elapsedTime); if (trim((string)$item->parMessage) != ''): printf(_(' Par message: %s'), (string)$item->parMessage); endif; ?>
				</li>
				<?php endforeach; ?>
			<?php endif; ?>

		</ul>
		<?php endif; ?>

		<div id="content">
			<div id="queuestatus" class="queuebox first">
				<?php printf(_('Queued: %s items totalling %sMB'), $c->queueLength, $c->queueSize); ?>
			</div>
			<form method="post" action="<?php echo $self; ?>" onsubmit="confirmorder(this)">
				<div id="controls">
					<span id="refresher">
					<input type="checkbox" name="refresh" id="refresh" onclick="if (this.checked) { createCookie('HHRefresh', 1, 30);startRefresher(); } else { createCookie('HHRefresh', 0, 30);stopRefresher(); }" /> <label for="refresh" class="refresher"><?php echo _('Refresh every 15 seconds'); ?></label> <input type="button" value="<?php echo _('Refresh Now'); ?>" onclick="refreshNow();" />
					</span>
					<input type="submit" name="reorder" value="<?php echo _('Reorder Items'); ?>" /> <?php echo _('by'); ?> <select name="sorttype" id="sorttype" onchange="showstatus(this);"><option value="1"><?php echo _('Provided Order'); ?></option><option value="2"><?php echo _('Name'); ?></option><option value="3"><?php echo _('Size'); ?></option></select> <select name="sortdirection" id="sortdirection"><option value="1"><?php echo _('Ascending'); ?></option><option value="2"><?php echo _('Descending'); ?></option></select>
				</div>
			</form>
		</div>
		<script src="js/hellaworld_iphone.php" type="text/javascript"></script>
		<script type="text/javascript" charset="utf-8">
			function onDragStart(e,ui){
			//	console.log(e);
			//	console.log(ui);
			}
			function onDragStop(e,ui){
			//	resortQueue();
//			var expRg = RegExp("(.+)-(.+)");
			var hash = $("#downloadQueue").sortable("serialize",{attribute:'id', expression:/(.+)-(.+)/});
			resortQueue(hash);
			}
			var queueitems = $("#downloadQueue").sortable({axis:"y", containment:"parent", handle:"span.drag", stop:onDragStop, start:onDragStart, revert:true});
	
	
			function resortQueue(hash) {
				$.ajax({
					type : "POST",
					url : "index.php",
					data : hash,
					dataType : "json",
					success : function(json) {
						console.log(json);
						updateData(json);
					},
					error : function() {
						alert('<?php echo _('Apologies, there was an error while making the request'); ?>');
					}
				});
			}
				
			function touchHandler(event)
			{
			    var touches = event.changedTouches,
			        first = touches[0],
			        type = "";

			    switch(event.type)
			    {
			        case "touchstart": type = "mousedown"; break;
			        case "touchmove":  type="mousemove"; break;        
			        case "touchend":   type="mouseup"; break;
			        default: return;
			    }

			    //initMouseEvent(type, canBubble, cancelable, view, clickCount, 
			    //           screenX, screenY, clientX, clientY, ctrlKey, 
			    //           altKey, shiftKey, metaKey, button, relatedTarget);

			    var simulatedEvent = document.createEvent("MouseEvent");
			    simulatedEvent.initMouseEvent(type, true, true, window, 1, 
			                              first.screenX, first.screenY, 
			                              first.clientX, first.clientY, false, 
			                              false, false, false, 0/*left*/, null);

			    first.target.dispatchEvent(simulatedEvent);
			    event.preventDefault();
			}
			function mapTouches() 
			{
				$('span.drag').each(function(e,ev){
				    ev.addEventListener("touchstart", touchHandler, true);
				    ev.addEventListener("touchmove", touchHandler, true);
				    ev.addEventListener("touchend", touchHandler, true);
				    ev.addEventListener("touchcancel", touchHandler, true);    
				})
			}
			mapTouches();
			
//			setupDragNodes();
			
		</script>
	</body>
</html>
