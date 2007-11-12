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

	require_once 'xmlrpclib/xmlrpc.inc';

	if (!defined('HELLA_DEBUG')) {
		define('HELLA_DEBUG', false);
	}

	Class HellaController {

		private $client = false;

		// Hella NZB Status Info
		public $version = 0;
		public $uptime = 0;
		public $totalMB = 0;
		public $totalFiles = 0;
		public $totalSegments = 0;
		public $totalNZBs = 0;
		public $paused = false;
		public $transferRate = 0;
		public $downloadCount = 0;
		public $downloads = array();
		public $processCount = 0;
		public $processing = array();
		public $queueLength = 0;
		public $queueSize = 0;
		public $queue = array();
		public $logLength = 0;
		public $log = array();

		public function __construct($host, $port, $username, $password) {
			$this->client = new xmlrpc_client('', $host, $port);
			$this->client->setCredentials($username, $password);
			if (HELLA_DEBUG) {
				$this->client->setDebug(1);
			}
			$this->getStatus();
		}

		public function getStatus() {
			$r = $this->sendCommand('status');
			$info = php_xmlrpc_decode($r->value());
			if (HELLA_DEBUG) {
				print_r($info);
			}
			$this->version = $info['version'];
			$this->uptime = $info['uptime'];
			$this->totalMB = $info['total_dl_mb'];
			$this->totalFiles = $info['total_dl_files'];
			$this->totalSegments = $info['total_dl_segments'];
			$this->totalNZBs = $info['total_dl_nzbs'];
			$this->rateLimit = $info['maxrate'];
			$this->paused = $info['is_paused'];
			$this->completed = $info['percent_complete'];
			$this->remaining = $info['queued_mb'];
			$this->transferRate = $info['rate'];
			$this->eta = $info['eta'];
			$this->downloadCount = count($info['currently_downloading']);
			if ($this->downloadCount > 0) {
				$this->downloads = array();
				foreach ($info['currently_downloading'] as $download) {
					$download['nzbName'] = str_replace('_', ' ', $download['nzbName']);
					$this->downloads[] = $download;
				}
			}
			$this->processCount = count($info['currently_processing']);
			if ($this->processCount > 0) { 
				$this->processing = array();
				foreach($info['currently_processing'] as $process) {
					$this->processing[] = str_replace('_', ' ', $process['nzbName']);
				}
			}

			$this->queueLength = count($info['queued']);
			$this->queueSize = 0;

			if ($this->queueLength > 0) {
				$this->queue = array();
				foreach($info['queued'] as $queued) {
					$this->queueSize += $queued['total_mb'];
					if ($this->transferRate == 0) {
						$queued['eta'] = 0;
					} else {
						$queued['eta'] = round(($this->queueSize * 1024) / $this->transferRate);
					}
					$this->queue[] = $queued;
				}
			}

			$this->logLength = count($info['log_entries']);
			if ($this->logLength > 0) {
				$this->log = array();
				foreach ($info['log_entries'] as $log) {
					foreach($log as $type => $line) {
						$this->log[] = trim($type . ': ' . str_replace(array("\r", "\n"), '', $line));
					}
				}
			}
		}

		private function sendCommand($command, $arguments = '') {
			$atmp = array();
			if (is_array($arguments)) {
				foreach($arguments as $argument) {
					$atmp[] = new xmlrpcval($argument, 'int');
				}
				$arguments = $atmp;
			} elseif ($arguments != '') {
				$arguments = array(new xmlrpcval($arguments, 'int'));
			}
			$f = new xmlrpcmsg($command, $arguments);
			$r = $this->client->send($f);
			if ($r->faultCode() != 0) {
				throw new Exception('XML RPC Error: ' . $r->faultString());
			}
			return $r;
		}

		public function cancel() {
			$this->sendCommand('cancel');
		}

		public function clear($download = false) {
			$this->sendCommand('clear', $download);
		}

		public function resume() {
			$this->sendCommand('continue');
		}

		public function dequeue($nzbid) {
			if ($nzbid === false) throw new Exception('Invalid ID provided');
			$this->sendCommand('dequeue', $nzbid);
		}

		public function down($nzbid, $shift = 1) {
			if ($nzbid === false) throw new Exception('Invalid ID provided');
			$this->sendCommand('down', array($nzbid, $shift));
		}

		public function enqueue($filename) {
			if ($nzbid === false) throw new Exception('Invalid ID provided');
			$this->sendCommand('enqueue', $filename);
		}

		public function enqueueNewzbin($nzbid) {
			if ($nzbid === false) throw new Exception('Invalid ID provided');
			if (!ctype_digit($nzbid)) {
				throw new Exception('Invalid Newzbin article ID provided');
			}
			$this->sendCommand('enqueuenewzbin', $nzbid);
		}
		
		public function enequeueURL($url) {
			if (preg_match('/((https?):\/\/)?(([A-Z0-9][A-Z0-9_-]*)((\.[A-Z0-9][A-Z0-9_-]*)+)?\.(aero|biz|cat|com|coop|info|jobs|mobi|museum|name|net|org|pro|travel|gov|edu|mil|int|[0-9]{1}|[A-Z]{2}))(:(\d+))?(\/([^ ]*)?| |$)/i', $url) < 1) {
				throw new Exception('Invalid URL provided');
			}
			$this->sendCommand('enqueueurl', $url);
		}

		public function force($nzbid) {
			if ($nzbid === false) throw new Exception('Invalid ID provided');
			$this->sendCommand('force', $nzbid);
		}

		public function last($nzbid) {
			if ($nzbid === false) throw new Exception('Invalid ID provided');
			$this->sendCommand('last', $nzbid);
		}

		public function listNZBs($excludeids = false) {
			$this->sendCommand('list', $excludeids);
		}

		public function setRate($rate) {
			if (!ctype_digit($rate)) {
				throw new Exception('Invalid rate provided');
			}
			$this->sendCommand('maxrate', $rate);
		}

		public function move($nzbid, $index) {
			if ($nzbid === false) throw new Exception('Invalid ID provided');
			$this->sendCommand('move', array($nzbid, $index));
		}


		public function first($nzbid) {
			if ($nzbid === false) throw new Exception('Invalid ID provided');
			$this->sendCommand('next', $nzbid);
		}

		public function pause() {
			$this->sendCommand('pause');
		}

		public function shutdown() {
			$this->sendCommand('shutdown');
		}

		public function up($nzbid, $shift = 1) {
			if ($nzbid === false) throw new Exception('Invalid ID provided');
			$this->sendCommand('up', array($nzbid, $shift));
		}

		public static function formatTimeStamp($difference){

			$days = floor($difference/86400);
			$difference = $difference - ($days*86400);

			$hours = floor($difference/3600);
			$difference = $difference - ($hours*3600);

			$minutes = floor($difference/60);
			$difference = $difference - ($minutes*60);

			$seconds = $difference;
			$output = '';

			if ($days > 1) {
				$output = $days . ' Days, ';
			} elseif ($days > 0) {
				$output = $days . ' Day, ';
			}

			$output .= $hours . ':' . str_pad($minutes, 2, '0', STR_PAD_LEFT) . ':' . str_pad($seconds, 2, '0', STR_PAD_LEFT);
			return $output;

		}
	}
?>
