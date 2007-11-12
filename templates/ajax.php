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
