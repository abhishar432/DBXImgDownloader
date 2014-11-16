#!/usr/bin/php -q
<?php
require_once './ImgDownloaderInput.php';
require_once './ImageDetails.php';
require_once './dropbox-sdk/Dropbox/autoload.php';
use \Dropbox as dbx;

interface ImageDownloader{
	public function download();
}

class DropBoxImageDownloader{
	private $input;
	private $image;
	private $dbxClient;

	public function __construct($image, $input, $dbxClient){
		$this->image = $image;
		$this->input = $input;
		$this->dbxClient = $dbxClient;
	}
	public function download(){
		$timeLng = strtotime($this->image->client_mtime);
		$year = date('Y',$timeLng);
		$month = date('m',$timeLng);
		$day = date('d',$timeLng);
		$path = $this->input->outputDir.'/'.$this->input->accountName.'/'.$year.'/'.$month.'/'.$day.'/';
		if(!file_exists($path)){
			mkdir($path,0777, true);
		}
		$fileName = $path.$this->image->name;
		if (!file_exists($fileName)){
			$handle = fopen($fileName, "w+b");
			$fileMetadata = $this->dbxClient->getFile($this->image->path, $handle);
			fclose($handle);
			println ("Image ".$this->image->path." successfully downloaded to ".$path);
		}else{
			println ("Image ".$this->image->path." already exists in ".$path);
		}
	}
}
?>