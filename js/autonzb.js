// $Id$

function autonzb(url) {
	if (location.hostname != 'www.newzbin.com' && location.hostname != 'v3.newzbin.com') {
		window.alert('Cannot send a non-newzbin page to HellaWorld RSS.');
		return;
	}

	var head = document.getElementsByTagName('head');
	if (head.length == 0) {
		window.alert('Cannot locate <head> element!');
		return;
	}
	head = head[0];

	var links = head.getElementsByTagName('link');
	if (links.length == 0) {
		window.alert('Cannot locate <link> elements!');
		return;
	}

	var link;
	for (x = 0; x < links.length; x++) {
		link = links[x];
		if (link.getAttribute('type') == 'application/atom+xml') {
			var feedUrl = link.getAttribute('href');
			feedUrl = 'http://v3.newzbin.com'+feedUrl;
			window.location.href=url+'?addfeed='+escape(feedUrl);
			return;
		}
	}
}

autonzb(hw_url);