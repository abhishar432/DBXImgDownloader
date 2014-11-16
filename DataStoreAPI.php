<?php
// Data Store API Interface to persist and retrieve account information
interface DataStoreAPI{
	public function save($input);
	public function get();
}
// Data Store API Implementation to persist and retrieve account information in/from local file
class LocalFileDataStoreAPI implements DataStoreAPI{
	private $storepath = ".ds.json";
	public function save($input){
		if (is_null($input)) throw new Exception("input to persist is null", 1);
		$content = json_encode($input);
		$fp = fopen($this->storepath, 'w');
		fwrite($fp, $content);
		fclose($fp);
	}
	public function get(){
		if(file_exists($this->storepath)){
			$string = file_get_contents($this->storepath);
			$json_a = json_decode($string, true);
			return ImgDownloaderInput::fillJson($json_a);
		}else{
			return new ImgDownloaderInput();
		}
	}
}
// Factory to retrieve different DataStore API implementation, Use 'local' 
// to retrieve date store implementation for local file system
class DataStoreAPIFactory{
	public static function getInstance($type){
		if ($type =='local') return new LocalFileDataStoreAPI();
		else return new LocalFileDataStoreAPI();
	}
}
?>