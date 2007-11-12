	var refresh = false;

	function startRefresher() {
		refresh = true;
		setTimeout('refresher()', 15000);
	}

	function stopRefresher() {
		refresh = false;
	}

	function refresher() {
		if (refresh) {
			new Ajax.Updater('info', 'index.php?refresher=1');
			new Ajax.Updater('scriptbox', 'index.php?refresher=2', {evalScripts: true});
			new Ajax.Updater('log', 'index.php?refresher=3');
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

	function setstyles() {
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
		if (document.getElementById('refresher') != null) {
			document.getElementById('refresher').style.display = 'inline';
		}
		if (document.getElementById('sortdirection') != null) {
			document.getElementById('sortdirection').style.display = 'none';
		}
		if (refreshcookie == 1) {
			document.getElementById('refresh').checked = true;
			startRefresher();
		}
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
