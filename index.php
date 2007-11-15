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

	function ipInRange($range, $address) {
		$range = str_replace(array(' ', "\r", "\n"), '', $range);
		list($a, $b, $c, $d) = explode('.', $address);
		$check = ($a << 24) + ($b << 16) + ($c << 8) + $d;
		foreach(explode(',', $range) as $ip) {
			list($base, $bits) = explode('/', $ip);
			list($a, $b, $c, $d) = explode('.', $base);

			$i = ($a << 24) + ($b << 16) + ($c << 8) + $d;
			$mask = $bits == 0 ? 0 : (~0 << (32 - $bits));
			$low = $i & $mask;
			$high = $i | (~$mask & 0xFFFFFFFF);

			if ($check >= $low && $check <= $high) {
				return true;
			}
		}
		return false;
	}

	try {

		if (file_exists('config.php')) {
			require_once 'config.php';
		} else {
			throw new Exception('config.php not found, please configure HellaWorld');
		}

		if (isset($config['auth']) && !empty($config['auth'])) {
			$auth = strtolower($config['auth']);
			if ($auth == 'open' || $auth == 'hybrid') {
				if (!isset($config['username']) || !isset($config['password']) || empty($config['username']) || empty($config['password'])) {
					throw new Exception('Authentication types open and hybrid require a password to be set');
				}
			}
		} else {
			$auth = 'open';
		}

		if (!function_exists('_')) {
			function _($string) {
				return $string;
			}
		} else {
			putenv('LC_ALL=' . $config['language'] . '.UTF-8');
			setlocale(LC_ALL, $config['language'] . '.UTF-8');
			bindtextdomain('messages', 'locale');
			textdomain('messages');
		}

		$hellaworldversion = "1.10-SVN";
		if (!isset($config['iprange'])) {
			$iprange = '192.168.0.0/16,10.0.0.0/8,172.168.0.0/12';
		} else {
			$iprange = $config['iprange'];
		}
		$protocol = (($_SERVER['SERVER_PORT'] == 443) ? 'https' : 'http');

		require_once 'classes/HellaController.php';

		if ($auth == 'exclusive' && !ipInRange($iprange, $_SERVER['REMOTE_ADDR'])) {
			throw new Exception('IP Address is not in an allowed range');
		} elseif ($auth == 'closed' || ($auth == 'hybrid' && !ipInRange($iprange, $_SERVER['REMOTE_ADDR']))) {
			session_start();
			if (!array_key_exists('PHP_AUTH_USER', $_SERVER) || empty($_SERVER['PHP_AUTH_USER'])) {
				$_SESSION['login'] = 0;
				header('WWW-Authenticate: Basic realm="HellaWorld"');
				header('HTTP/1.0 401 Unauthorized');
				throw new Exception(_('Unauthorized access'));
			} else {
				$config['username'] = $_SERVER['PHP_AUTH_USER'];
				$config['password'] = $_SERVER['PHP_AUTH_PW'];
			}
			try {
				$c = new HellaController($config['host'], $config['port'], $config['username'], $config['password']);
			} catch (Exception $e) {
				if ($e->getCode() == 5) {
					$_SESSION['login'] ++;
					if ($_SESSION['login'] < 3) {
						header('WWW-Authenticate: Basic realm="HellaWorld"');
						header('HTTP/1.0 401 Unauthorized');
						throw new Exception(_('Unauthorized access'));
					} else {
						$_SESSION['login'] = 0;
						header('HTTP/1.0 403 Forbidden');
						throw new Exception(_('Access Forbidden'));
					}
				} else {
					throw new Exception($e->getMessage());
				}
			}
		} else {
			$c = new HellaController($config['host'], $config['port'], $config['username'], $config['password']);
		}
		$self = htmlentities($_SERVER['PHP_SELF']);

		if (array_key_exists('id', $_GET) && preg_match('/^\d+$/', $_GET['id'])) {
			$nzbid = $_GET['id'];
		} else {
			$nzbid = false;
		}
		if (array_key_exists('action', $_GET) && preg_match('/^\d+$/', $_GET['action'])) {
			switch ($_GET['action']) {
				case 1:
					$c->dequeue($nzbid);
					break;
				case 2:
					$c->up($nzbid);
					break;
				case 3:
					$c->down($nzbid);
					break;
				case 4:
					$c->first($nzbid);
					break;
				case 5:
					$c->last($nzbid);
					break;
				case 6:
					$c->force($nzbid);
					break;
				case 7:
					$c->pause();
					break;
				case 8:
					$c->resume();
					break;
				case 9:
					$c->cancel();
					break;
				case 10:
					$c->clear();
					break;
				case 11:
					$c->shutdown();
					throw new Exception('HellaNZB Has been shut down');
			}
			header('Location: ' . $protocol . '://' . $_SERVER['HTTP_HOST'] . $self);
		}

		$x = false;

		if (isset($config['showfinished']) && $config['showfinished'] && file_exists('completed.xml')) {
			$xmlstring = file_get_contents('completed.xml');
			if ($xmlstring) {
				$x = @new SimpleXMLElement($xmlstring);
			}
		}

		if (array_key_exists('refresher', $_GET) && preg_match('/^\d+$/', $_GET['refresher'])) {
			if ($_GET['refresher'] == 1) {
				include 'templates/json.php';
			}
			die();
		}

		if (array_key_exists('reorder', $_POST) && array_key_exists('sorttype', $_POST) && preg_match('/^\d+$/', $_POST['sorttype'])) {
			$type = $_POST['sorttype'];
			if ($type == 1 && array_key_exists('order', $_POST) && is_array($_POST['order'])) {
				arsort($_POST['order']);
				$c->multiCallStart();
				foreach($_POST['order'] as $queueindex => $neworder) {
					if (preg_match('/^\d+$/', $neworder) && $queueindex != $neworder) {
						$c->move($c->queue[$queueindex]['id'], $neworder);
					}
				}
				$c->multiCallCommit();
			} elseif (array_key_exists('sortdirection', $_POST) && preg_match('/^\d+$/', $_POST['sortdirection'])) {
				$sortdir = $_POST['sortdirection'];
				if ($type == 2) {
					$nzbName = array();
					foreach($c->queue as $queueitem) {
						$nzbName[] = $queueitem['nzbName'];
					}
					natcasesort($nzbName);
					if ($sortdir == 2) {
						$nzbName = array_reverse($nzbName);
					}
					$i = 1;
					$c->multiCallStart();
					while (list($id) = each($nzbName)) {
						$c->move($c->queue[$id]['id'], $i++);
					}
					$c->multiCallCommit();
				} elseif ($type == 3) {
					$size = array();
					foreach($c->queue as $queueitem) {
						$size[] = $queueitem['total_mb'];
					}
					natsort($size);
					if ($sortdir == 2) {
						$size = array_reverse($size);
					}
					$i = 1;
					$c->multiCallStart();
					while (list($id) = each($size)) {
						$c->move($c->queue[$id]['id'], $i++);
					}
					$c->multiCallCommit();
				}
			}
			header('Location: ' . $protocol . '://' . $_SERVER['HTTP_HOST'] . $self);
		}

		if (array_key_exists('queue', $_POST) && is_array($_POST['queue'])) {
			$tmp1 = array();
			$tmp2 = array();
			foreach($c->queue as $pos => $queue) {
				$tmp1[$pos] = $queue['id'];
				$tmp2[$pos] = substr($_POST['queue'][$pos], 6);
			}
			$diff = array_diff_assoc($tmp2, $tmp1);
			reset($diff);
			$fkey = key($diff);
			end($diff);
			$lkey = key($diff);
			if ($diff[$fkey] == $tmp1[$lkey]) {
				$c->move($diff[$fkey], $fkey + 1); // moving up
			} else {
				$c->move($diff[$lkey], $lkey + 1); // moving down
			}
			$c->getStatus();
			include 'templates/json.php';
			die();
		}

		if (array_key_exists('nzbdownload', $_GET)) {
			$nzbdownload = trim($_GET['nzbdownload']);
			if (preg_match('/^\d+$/', $nzbdownload)) {
				$c->enqueueNewzbin($nzbdownload);
			} else {
				$c->enqueueURL($nzbdownload);
			}
			header('Location: ' . $protocol . '://' . $_SERVER['HTTP_HOST'] . $self);
		}

		if (array_key_exists('bookmarklet', $_GET)) {
			if (preg_match('/((https?):\/\/)?(([A-Z0-9][A-Z0-9_-]*)((\.[A-Z0-9][A-Z0-9_-]*)+)?)(:(\d+))?(\/([^ ]*)?| |$)/i', $_GET['bookmarklet']) < 1) {
				throw new Exception('Invalid bookmarklet URL');
			}
			$match = array();
			if (preg_match('/browse\/post\/(\d+)\/?$/', $_GET['bookmarklet'], $match) > 0) {
				$c->enqueueNewzbin($match[1]);
			}
			header('Location: ' . $_GET['bookmarklet']);
		}

		if (array_key_exists('maxrate', $_GET) && preg_match('/^\d+$/', $_GET['maxrate'])) {
			$c->setRate($_GET['maxrate']);
			header('Location: ' . $protocol . '://' . $_SERVER['HTTP_HOST'] . $self);
		}

		if (array_key_exists('info', $_GET) && preg_match('/^\d+$/', $_GET['info'])) {
			$index = $_GET['info'] - 1;
			include 'templates/rarpass.php';
			die();
		}

		if (array_key_exists('password', $_POST)) {
			if (PHP_VERSION < 6.0 && get_magic_quotes_gpc()) {
				$password = stripslashes($_POST['password']);
			} else {
				$password = $_POST['password'];
			}
			$c->setRarPass($nzbid, $password);
			$submitted = true;
			include 'templates/rarpass/php';
			die();
		}

		if (array_key_exists('clearnzbs', $_POST)) {
			if (file_exists('completed.xml')) {
				if (isset($x->item)) {
					unset($x->item);
					$fp = @fopen('completed.xml', 'w');
					if (!$fp) {
						throw new Exception(_('Unable to write to completed.xml, please check permissions'));
					}
					fwrite($fp, $x->asXML());
					fclose($fp);
				}
			}
			header('Location: ' . $protocol . '://' . $_SERVER['HTTP_HOST'] . $self);
		}

		if (array_key_exists('removefinished', $_GET) && preg_match('/^\d+$/', $_GET['removefinished']) && $x) {
			$id = --$_GET['removefinished'];
			if (isset($x->item[$id])) {
				unset($x->item[$id]);
				$fp = @fopen('completed.xml', 'w');
				if (!$fp) {
					throw new Exception(_('Unable to write to completed.xml, please check permissions'));
				}
				fwrite($fp, $x->asXML());
				fclose($fp);
				header('Location: ' . $protocol . '://' . $_SERVER['HTTP_HOST'] . $self);
			} else {
				throw new Exception(_('Invalid ID provided'));
			}
		}

	} catch (Exception $e) {
		$errormessage = $e->getMessage();
		include 'templates/error.php';
		die();
	}

	include 'templates/default.php';
?>
