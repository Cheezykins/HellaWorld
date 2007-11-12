	// $Id$

	var refresh = false;
	
	document.styleSheets[1].disabled = true;

	function startRefresher() {
		refresh = true;
		setTimeout('refresher()', 15000);
	}

	function stopRefresher() {
		refresh = false;
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
						alert('Apologies, there was an error while making the request');
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
		var info = "<h2>Currently Downloading</h2>";
		if (json.downloading != null) {
			info += "<div>" + json.downloading + "</div>";
			info += '<div class="queuestats"><img alt="progress bar" src="progress.php?percentage=' + json.completed + '"/> ' + json.completed + '% complete</div>';
			info += '<div class="queuestats">' + json.remaining + 'MB of ' + json.size + 'MB remaining</div>';
			info += '<div class="queuestats" style="margin-bottom: 5px;">';
			if (json.paused) {
				info += 'PAUSED';
			} else {
				info += 'ETA ' + json.eta + ' at ' + json.transferRate + 'KB/s';
			}
			info += '</div>'
		} else {
			info += '<div style="margin-bottom: 5px;">Nothing</div>';
		}
		info += '<h2>Currently Processing</h2>';
		if (json.processing != null) {
			info += '<div>' + json.processing + '</div>';
		} else {
			info += '<div>Nothing</div>';
		}
		document.getElementById('info').innerHTML = info;
		if (json.queuelength > 0) {
			var queue = '';
			for (i = 0; i < json.queuelength; i ++) {
				queue += '<li id="order_' + json.queue[i].id + '" class="queuebox">';
				queue += '<div class="orderform"><input type="text" name="order[]" onchange="stopRefresher();" value="' + json.queue[i].index + '" /></div>';
				queue += '<div class="queuetitle">' + json.queue[i].nzbName + '</div>';
				queue += '<ul class="queuecontrols"><li class="handle">Drag me</li><li class="control"><a href="index.php?info=' + json.queue[i].index + '&KeepThis=true&TB_iframe=true&height=200&width=500" class="thickbox"><img src="images/information.png" alt="Info" /></a></li><li class="control"><a title="Cancel" href="' + json.path + '?action=1&amp;id=' + json.queue[i].id + '"><img src="images/delete.png" alt="Cancel" /></a></li><li class="control"><a title="Up" href="' + json.path + '?action=2&amp;id=' + json.queue[i].id + '"><img src="images/up.png" alt="Up" /></a></li><li class="control"><a title="Down" href="' + json.path + '?action=3&amp;id=' + json.queue[i].id + '"><img src="images/down.png" alt="Down" /></a></li><li class="control"><a title="Top" href="' + json.path + '?action=4&amp;id=' + json.queue[i].id + '"><img src="images/top.png" alt="Top" /></a></li><li class="control"><a title="Bottom" href="' + json.path + '?action=5&amp;id=' + json.queue[i].id + '"><img src="images/bottom.png" alt="Bottom" /></a></li><li class="control"><a title="Force" href="' + json.path + '?action=6&amp;id=' + json.queue[i].id + '"><img src="images/force.png" alt="Force" /></a></li></ul>';
				queue += '<div class="queuestats">' + json.queue[i].totalmb + 'MB';
				if (json.transferRate > 0) {
					queue += ' ETA: ' + json.queue[i].eta + '</div>';
				} else {
					queue += '</div>';
				}
				queue += '</li>';
			}
			document.getElementById('queue').innerHTML = queue;
			makeSortable();
		}
		var log = '';
		for (line in json.log) {
			log += line + '\n';
		}
		document.getElementById('log').innerTEXT = log;
		tb_init('a.thickbox');
	}

	function refresher() {
		if (refresh) {
			$.getJSON('index.php?refresher=1', function(json) { updateData(json); }); 
			setTimeout('refresher()', 15000);
		}
	}

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

	function init() {
		logcookie = readCookie('HHLogShow');
		refreshcookie = readCookie('HHRefresh');
		var hidelog = 'none';
		var showlog = 'block';
		var log = 'none';
		if (logcookie == 1) {
			var hidelog = 'block';
			var showlog = 'none';
			var log = 'block';
		}
		if (document.getElementById('hidelog') != null) {
			document.getElementById('hidelog').style.display = hidelog;
		}
		if (document.getElementById('log') != null) {
			document.getElementById('log').style.display = log;
		}
		if (document.getElementById('showlog') != null) {
			document.getElementById('showlog').style.display = showlog;
		}
		if (refreshcookie == 1) {
			document.getElementById('refresh').checked = true;
			startRefresher();
		}
		makeSortable();
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
