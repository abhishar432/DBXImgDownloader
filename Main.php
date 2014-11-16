#!/usr/bin/php -q
<?php
@ob_end_clean();
require_once './ImgDownloaderInput.php';
require_once './ImageDetails.php';
require_once './DataStoreAPI.php';
require_once './Authenticator.php';
require_once './ImageDownloader.php';
require_once './dropbox-sdk/Dropbox/autoload.php';
use \Dropbox as dbx;

function println($msg){
	fwrite(STDOUT, "$msg\n");
}

// Method to fetch Application Key and Secret
function readAppInfo() {
	return dbx\AppInfo::loadFromJsonFile("config.json");
}
// Method to fetch all the images present in the account
function fetchAllImages($path, $dbxClient){
	if(is_null($path)) throw new Exception("Path is null", 1);
	if(is_null($dbxClient)) throw new Exception("Dropbox client is null", 1);
	$images = array();
	
	$folderMetadata = $dbxClient->getMetadataWithChildren($path);
	if(!is_null($folderMetadata) && !is_null($folderMetadata[contents])){
		foreach ($folderMetadata[contents] as $value){
			$isDir = $value[is_dir];
			$dpxPath = $value[path];
			if($isDir == 1){
				$images = array_merge($images, fetchAllImages($dpxPath,$dbxClient));
			}else{
				$imageName = substr($dpxPath,strrpos($dpxPath, "/") + 1);
				$client_mtime = $value[client_mtime];
				$images[] = new ImageDetails($dpxPath,$client_mtime,$imageName);
			}
		}
	}
	return  $images;
}

// Method to download files to local file system
function downloadFile($image, $input, $dbxClient){
	try{
		$downloader = new DropBoxImageDownloader($image, $input, $dbxClient);
		$downloader->download();
	}catch (Exception $e) {
		echo 'Caught exception: ',  $e->getMessage(), "\n";
	}
}


/*
FOR MULTITHREADING
define ('THREAD_COUNT', 3);
function downloadUsingMultithread($images, $input, $dbxClient){
	$imagesTobeProcessed = count($images) - 1;
	$i = 0;
	// Initializing Worker pool
	$workers = [];
	$count = min($imagesTobeProcessed,THREAD_COUNT - 1);
	foreach ($images as $image) {
		$workers[$i] = new DropBoxImageDownloader($image, $input, $dbxClient);
		$workers[$i]->start();
		if ($i == $count){
			foreach (range(0, $i) as $j) {
			  $workers[$j]->join();
			}
			$i = 0;
			$count = min($imagesTobeProcessed,THREAD_COUNT- 1);
		}else{
			$i++;
		}
		$imagesTobeProcessed --; 
	}
}*/

//Setting up the timezone to utc
date_default_timezone_set('UTC');

// Setting up the datastore api for local storage
$datastore = DataStoreAPIFactory::getInstance("local");

// Fetch input from datastore
$input = $datastore->get();
$accountName = $input->accountName;
$accessToken = $input->accessToken;

//Reading App Information for appkey and appsecret
$appInfo = readAppInfo();
println("Welcome to Dropbox Downloader CLI ".$accountName);

//Using appropriate authenticator methods based on input
if (is_null($accountName)){
	$authenticator = AuthenticatorFactory::getDropBoxAuthenticator($input,$appInfo);
}else{
	$authenticator = AuthenticatorFactory::getNoAuthRequired($input,$appInfo);
}
$authenticator->getAccessKey();

// Creating client using AccessKey
$dbxClient = new dbx\Client($input->accessKey, "PHP-Example/1.0");
$accountInfo = $dbxClient->getAccountInfo();

// Saving the input if necessary
if ($authenticator->hasChanged()){
	$input->accountName = str_replace(" ", "", $accountInfo[display_name]);
	$datastore->save($input);
}

println("Welcome $accountInfo[display_name]\n");

//Fetching all Images
$images = fetchAllImages('/',$dbxClient);

foreach ($images as $image) {
	downloadFile($image, $input,$dbxClient);
}

println("\nDownloaded ".count($images)." images to the output directory ".$input->outputDir.'/'.$input->accountName);
?>