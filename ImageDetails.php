<?php
// Holder for details related to images to be downloaded
class ImageDetails{
	public $path;
	public $client_mtime;
	public $name;

	public function __construct($path,$client_mtime,$name)
	{
		$this->path = $path;
		$this->client_mtime = $client_mtime;
		$this->name = $name;
	}

	public function toString(){
		return "Path= ".$this->path." ,  Creation Time= ".$this->client_mtime
		." ,  Name= ".$this->name;
	}
}
?>