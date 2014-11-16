<?php
// Holder for necessary account info, outputdir and access key
// required for Downloader to work
class ImgDownloaderInput{
	public $accessKey;
	public $outputDir = ".";
	public $accountName;

	public static function fill($accessKey,$outputDir,$accountName){
		$instance = new self();
		$instance->accessKey = $accessKey;
		$instance->outputDir = $outputDir;
		$instance->accountName = $accountName;
		return $instance;
	}
	
	public static function fillJson($json_a)
	{
		$instance = new self();
		$instance->outputDir=$json_a[outputDir];
		$instance->accessKey=$json_a[accessKey];
		$instance->accountName=$json_a[accountName];
		return $instance;
	}

	public function toString(){
		return "Access Key= ".$this->accessKey." , Output Dir= ".$this->outputDir
		." , Account Name= ".$this->accountName;
	}
}
?>