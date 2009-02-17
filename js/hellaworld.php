<?php	

	// $Id$
	include '../config.php';

	if (!function_exists('_')) {
		function _($string) {
			return $string;
		}
	} else {
		putenv('LC_ALL=' . $config['language'] . '.UTF-8');
		setlocale(LC_ALL, $config['language'] . '.UTF-8');
		bindtextdomain('messages', '../locale');
		textdomain('messages');
	}

?>

	var refresh = false;

	var node = document.styleSheets[1];
	if ($.browser.safari || $.browser.opera) {
		for (i = 0; node.cssRules && i < node.cssRules.length; i++) {
			node.cssRules[i].style.removeProperty('display');
			$('#log').css('margin-top', '0px');
		}
	} else {
	   	node.disabled = true;
	}

	function startRefresher() {
		if (refresh) {
			clearTimeout(refresh);
		}
		refresh = setTimeout('refresher()', 15000);
	}

	function stopRefresher() {
		if (refresh) {
			clearTimeout(refresh);
			refresh = false;
		}
	}

	function makeSortable() {
		$('#queue').Sortable({
			accept : 'queuebox',
			handle : '.handle',
			opacity: 0.5,
			onchange: function(queue) {
				$.ajax({
					type : "POST",
					url : "index.php",
					data : queue[0].hash,
					dataType : "json",
					success : function(json) {
						updateData(json);
					},
					error : function() {
						alert('<?php echo _('Apologies, there was an error while making the request'); ?>');
					}
				});
			},
			onStart: function() {
				stopRefresher();
			},
			onStop: function() {
				refreshcookie = readCookie('HHRefresh');
				if (refreshcookie == 1) {
					startRefresher();
				}
			}
		})
	}

	function updateData(json) {
		var status = "<h1>HellaWorld</h1>";
		status += '<div><p><?php printf(_('HellaNZB %s - Uptime: %s'), "' + json.status.version + '", "' + json.status.uptime + '"); ?></p>';
		status += '<p><?php printf(_('Downloaded: %s files in %s segments totalling %sMB via %s NZB files'), "' + json.status.files + '", "' + json.status.segments + '", "' + json.status.totalmb + '", "' + json.status.totalnzbs + '"); ?></p></div>';
		$('#masthead').html(status);
		var info = "<h2><?php echo _('Currently Downloading'); ?></h2>";
		if (json.downloading != null) {
			info += '<div class="nzbname">' + json.downloading + '</div>';
			info += '<div class="queuestats"><img alt="progress bar" src="progress.php?percentage=' + json.completed + '"/> <?php printf(_('%s%% complete'), "' + json.completed + '"); ?></div>';
			info += '<div class="queuestats"><?php printf(_('%sMB of %sMB remaining'), "' + json.remaining + '", "' + json.size + '"); ?></div>';
			info += '<div class="queuestats" style="margin-bottom: 5px;">';
			if (json.paused) {
				info += '<?php echo _('PAUSED'); ?>';
			} else {
				info += '<?php printf(_('ETA %s at %sKB/s'), "' + json.eta + '", "' + json.transferRate + '"); ?>';
			}
			info += '</div>'
		} else {
			info += '<div style="margin-bottom: 5px;"><?php echo _('Nothing'); ?></div>';
		}
		info += '<h2><?php echo _('Currently Processing'); ?></h2>';
		if (json.processing != null) {
			for (i = 0; json.processing && i < json.processing.length; i ++) {
				info += '<div>' + json.processing[i] + '</div>';
			}
		} else {
			info += '<div><?php echo _('Nothing'); ?></div>';
		}
		$('#info').html(info);
		status = '<?php printf(_('Queued: %s items totalling %sMB'), "' + json.queuelength + '", "' + json.queuesize + '"); ?>';
		$('#queuestatus').html(status)
		var queue = '';
		if (json.queuelength > 0) {
			for (i = 0; i < json.queuelength; i ++) {
				queue += '<li id="order_' + json.queue[i].id + '" class="queuebox">';
				queue += '<div class="orderform"><input type="text" name="order[]" onchange="stopRefresher();" value="' + json.queue[i].index + '" /></div>';
				queue += '<div class="queuetitle">' + json.queue[i].nzbName + '</div>';
				queue += '<ul class="queuecontrols"><li class="handle"><?php echo _('Drag me'); ?></li><li class="control"><a href="index.php?info=' + json.queue[i].index + '&KeepThis=true&TB_iframe=true&height=200&width=500" class="thickbox"><img src="images/information.png" alt="Info" /></a></li><li class="control"><a title="Cancel" href="' + json.path + '?action=1&amp;id=' + json.queue[i].id + '"><img src="images/delete.png" alt="Cancel" /></a></li><li class="control"><a title="Up" href="' + json.path + '?action=2&amp;id=' + json.queue[i].id + '"><img src="images/up.png" alt="Up" /></a></li><li class="control"><a title="Down" href="' + json.path + '?action=3&amp;id=' + json.queue[i].id + '"><img src="images/down.png" alt="Down" /></a></li><li class="control"><a title="Top" href="' + json.path + '?action=4&amp;id=' + json.queue[i].id + '"><img src="images/top.png" alt="Top" /></a></li><li class="control"><a title="Bottom" href="' + json.path + '?action=5&amp;id=' + json.queue[i].id + '"><img src="images/bottom.png" alt="Bottom" /></a></li><li class="control"><a title="Force" href="' + json.path + '?action=6&amp;id=' + json.queue[i].id + '"><img src="images/force.png" alt="Force" /></a></li></ul>';
				queue += '<div class="queuestats"><?php printf(_('%sMB'), "' + json.queue[i].totalmb + '"); ?>';
				if (json.transferRate > 0) {
					queue += '<?php printf(_(' ETA: %s'), "' + json.queue[i].eta + '"); ?></div>';
				} else {
					queue += '</div>';
				}
					queue += '</li>';
			}
		} else {
			queue += '<li class="queuebox" style="text-align: center; font-weight: bold;"><?php echo _('Queue is empty'); ?></li>';
		}
		$('#queue').html(queue);
		makeSortable();
		if (json.showfinished) {
		$('#fragment-2').html(json.finished);
		}
		var log = '';
		for (i = 0; i < json.log.length; i++) {
			log += json.log[i] + '<br />';
		}
		$('#log').html(log);
		tb_init('a.thickbox');
	}

	function refresher() {
		if (refresh) {
			$.getJSON('index.php?refresher=1', function(json) { updateData(json); }); 
			clearTimeout(refresh);
			refresh = setTimeout('refresher()', 15000);
		}
	}

	function refreshNow() {
		$.getJSON('index.php?refresher=1', function(json) { updateData(json); });
	}

	function showstatus(obj) {
		if (obj.options[obj.selectedIndex].value > 1) {
			$('#sortdirection').css('display', 'inline');
		} else {
			$('#sortdirection').css('display', 'none');
		}
	}

	function confirmorder(obj) {
		var sorttype = obj.sorttype.options[obj.sorttype.selectedIndex].value;
		if (sorttype > 1) {
			return confirm('<?php echo _('This will reorder the entire queue, continue?'); ?>');
		} else {
			return true;
		}
	}

	function init() {
		tabcookie = parseInt(readCookie('HHTabShow'));
		refreshcookie = readCookie('HHRefresh');
		if (!tabcookie) {
			tabcookie = 1;
		}
		$('#tabcontainer').tabs(tabcookie, {
			fxFade: true,
			fxSpeed: 'fast',
			onClick: function(el) {
				createCookie('HHTabShow', el.accessKey, 30);
			}
		});
		if (refreshcookie == 1) {
			$('#refresh').attr('checked', 'checked');
			startRefresher();
		}
		makeSortable();
		$('#nzbdownload').focus();
	}

	function createCookie(name, value, days) {
		if (days) {
			var date = new Date();
			date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
			var expires = "; expires=" + date.toGMTString();
		} else {
			var expires = "";
		}
		document.cookie = name + "=" + value + expires + "; path=/";
	}

	function readCookie(name) {
		var nameEQ = name + "=";
		var ca = document.cookie.split(';');
		for (var i = 0; i < ca.length; i++) {
			var c = ca[i];
			while (c.charAt(0) == ' ') {
				c = c.substring(1, c.length);
			}
			if (c.indexOf(nameEQ) == 0) {
				return c.substring(nameEQ.length, c.length);
			}
		}
		return null;
	}
