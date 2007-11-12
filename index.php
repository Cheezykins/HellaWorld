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


	try {

		if (file_exists('config.php')) {
			require_once 'config.php';
		} else {
			throw new Exception('config.php not found, please configure HellaWorld');
		}

		$hellaworldversion = "1.7-SVN";
		$protocol = (($_SERVER['SERVER_PORT'] == 443) ? 'https' : 'http');

		require_once 'classes/HellaController.php';

		if (!array_key_exists('password', $config) || empty($config['password'])) {
			session_start();
			if (!array_key_exists('PHP_AUTH_USER', $_SERVER) || empty($_SERVER['PHP_AUTH_USER'])) {
				$_SESSION['login'] = 0;
				header('WWW-Authenticate: Basic realm="HellaWorld"');
				header('HTTP/1.0 401 Unauthorized');
				throw new Exception('Unauthorized access');
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
						throw new Exception('Unauthorized access');
					} else {
						$_SESSION['login'] = 0;
						header('HTTP/1.0 403 Forbidden');
						throw new Exception('Access Forbidden');
					}
				} else {
					throw new Exception($e->getMessage());
				}
			}
		} else {
			$c = new HellaController($config['host'], $config['port'], $config['username'], $config['password']);
		}
		$self = htmlentities($_SERVER['PHP_SELF']);

		if (array_key_exists('id', $_GET) && ctype_digit($_GET['id'])) {
			$nzbid = $_GET['id'];
		} else {
			$nzbid = false;
		}
		if (array_key_exists('action', $_GET) && ctype_digit($_GET['action'])) {
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
			}
			header('Location: ' . $protocol . '://' . $_SERVER['HTTP_HOST'] . $self);
		}

		$x = false;

		if (file_exists('completed.xml')) {
			$xmlstring = file_get_contents('completed.xml');
			if ($xmlstring) {
				$x = @new SimpleXMLElement($xmlstring);
			}
		}

		if (array_key_exists('refresher', $_GET) && ctype_digit($_GET['refresher'])) {
			if ($_GET['refresher'] == 1) {
				include 'templates/json.php';
			}
			die();
		}

		if (array_key_exists('reorder', $_POST) && array_key_exists('sorttype', $_POST) && ctype_digit($_POST['sorttype'])) {
			$type = $_POST['sorttype'];
			if ($type == 1 && array_key_exists('order', $_POST) && is_array($_POST['order'])) {
				arsort($_POST['order']);
				foreach($_POST['order'] as $queueindex => $neworder) {
					if (ctype_digit($neworder) && $queueindex != $neworder) {
						$c->move($c->queue[$queueindex]['id'], $neworder);
					}
				}
			} elseif (($type == 2 || $type == 3) && array_key_exists('sortdirection', $_POST) && ctype_digit($_POST['sortdirection'])) {
				$direction = ($_POST['sortdirection'] == 1) ? SORT_ASC : SORT_DESC;
				$nzbName = array();
				$total_mb = array();
				for($i = 0; $i < $c->queueLength; $i ++) {
					$nzbName[] = strtolower($c->queue[$i]['nzbName']);
					$total_mb[] = $c->queue[$i]['total_mb'];
				}
				if ($type == 2) {
					array_multisort($nzbName, $direction, SORT_STRING, $c->queue);
				} else {
					array_multisort($total_mb, $direction, $c->queue);
				}
				for($i = 1; $i <= $c->queueLength; $i ++) {
					$c->move($c->queue[$i - 1]['id'], $i);
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
			if (ctype_digit($nzbdownload)) {
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

		if (array_key_exists('maxrate', $_GET) && ctype_digit($_GET['maxrate'])) {
			$c->setRate($_GET['maxrate']);
			header('Location: ' . $protocol . '://' . $_SERVER['HTTP_HOST'] . $self);
		}

		if (array_key_exists('info', $_GET) && ctype_digit($_GET['info'])) {
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
						throw new Exception('Unable to write to completed.xml, please check permissions');
					}
					fwrite($fp, $x->asXML());
					fclose($fp);
				}
			}
			header('Location: ' . $protocol . '://' . $_SERVER['HTTP_HOST'] . $self);
		}

		if (array_key_exists('removefinished', $_GET) && ctype_digit($_GET['removefinished']) && $x) {
			$id = --$_GET['removefinished'];
			if (isset($x->item[$id])) {
				unset($x->item[$id]);
				$fp = @fopen('completed.xml', 'w');
				if (!$fp) {
					throw new Exception('Unable to write to completed.xml, please check permissions');
				}
				fwrite($fp, $x->asXML());
				fclose($fp);
				header('Location: ' . $protocol . '://' . $_SERVER['HTTP_HOST'] . $self);
			} else {
				throw new Exception('Invalid ID provided');
			}
		}

	} catch (Exception $e) {
		$errormessage = $e->getMessage();
		include 'templates/error.php';
		die();
	}

	include 'templates/default.php';
?>
