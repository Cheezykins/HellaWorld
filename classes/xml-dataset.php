<?php

/**
* XML_Dataset - Basic helper for saving data in XML.
* $Id$
*/
class XML_Dataset {
	protected $filename,
			  $auto_save,
			  $root;			  

	function __construct($filename, $auto_save = FALSE) {
		$this->filename = $filename;
		$this->auto_save = $auto_save;
		if (! file_exists($filename))
			throw new Exception('Invalid filename: '.$filename);
		$this->root = simplexml_load_file($filename);
	}

	public function __destruct() {
		if ($this->auto_save)
			$this->save();
	}
	
	public function __get($key) {
		return $this->root->$key;
	}
	
	public function asXML() {
		return $this->root->asXML();
	}

	function addNode($name, $attributes = array()) {
		$node = $this->root->addChild($name);
		foreach ($attributes as $key => $value)
			$node->addAttribute($key, $value);
		return $node;
	}
	
	function save() {
		if (! is_writable($this->filename))
			throw new Exception($this->filename.' is not writable.');
			
		if (! file_put_contents($this->filename, $this->root->asXML()))
			throw new Exception('Unable to write XML Dataset: '.$this->filename);
	}
}
