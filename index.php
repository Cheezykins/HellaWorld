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


	require_once 'config.php';
	require_once 'classes/HellaController.php';

	try {
		$c = new HellaController($config['host'], $config['port'], $config['username'], $config['password']);
		$orderoptions = '<option>-</option><option>' . implode('</option><option>', range(1, $c->queueLength)) . '</option>';
		$self = str_replace($_SERVER['DOCUMENT_ROOT'], '', $_SERVER['SCRIPT_FILENAME']);

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
			header('Location: http://' . $_SERVER['HTTP_HOST'] . $self);
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
					$nzbName[] = $c->queue[$i]['nzbName'];
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
			header('Location: http://' . $_SERVER['HTTP_HOST'] . $self);
		}

		if (array_key_exists('nzbdownload', $_POST) && ctype_digit($_POST['nzbdownload'])) {
			if (ctype_digit($_POST['nzbdownload'])) {
				$c->enqueueNewzbin($_POST['nzbdownload']);
			} elseif (preg_match('/((https?):\/\/)?(([A-Z0-9][A-Z0-9_-]*)((\.[A-Z0-9][A-Z0-9_-]*)+)?\.(aero|biz|cat|com|coop|info|jobs|mobi|museum|name|net|org|pro|travel|gov|edu|mil|int|[0-9]{1}|[A-Z]{2}))(:(\d+))?(\/([^ ]*)?| |$)/i', $_POST['nzbdownload']) > 0) {
				$c->enqueueURL($_POST['nzbdownload']);
			}
			header('Location: http://' . $_SERVER['HTTP_HOST'] . $self);
		}

		if (array_key_exists('maxrate', $_POST) && ctype_digit($_POST['maxrate'])) {
			$c->setRate($_POST['maxrate']);
			header('Location: http://' . $_SERVER['HTTP_HOST'] . $self);
		}
	} catch (Exception $e) {
		$errormessage = $e->getMessage();
	}

	include 'template.php';
?>
