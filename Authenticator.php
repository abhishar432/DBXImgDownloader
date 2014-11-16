<?php
require_once './dropbox-sdk/Dropbox/autoload.php';
use \Dropbox as dbx;
// Abstract class to handle authentication for user
abstract class Authenticate{
	public function getAccessKey(){
		while (!$this->isDone()) {
			$this->authenticate();
		}
	}
	abstract public function hasChanged();
	abstract protected function authenticate();
	abstract protected function isDone();
}

// Drop Box Authenticator using authentication code
class DropBoxAuth extends Authenticate{
	protected $input;
	protected $appInfo;
	private $complete = false;
	public function __construct($input,$appInfo){
		$this->input = $input;
		$this->appInfo = $appInfo;
	}

	protected function authenticate(){
		try{
			$webAuth = new dbx\WebAuthNoRedirect($this->appInfo, "PHP-Example/1.0");
			$authorizeUrl = $webAuth->start();

			echo "\n1. Go to: " . $authorizeUrl . "\n";
			echo "2. Click \"Allow\" (you might have to log in first).\n";
			echo "3. Copy the authorization code.\n";

			$authCode = \trim(\readline("Enter the authorization code here: "));
			$existOpDir = $this->input->outputDir;
			list($accessToken, $dropboxUserId) = $webAuth->finish($authCode);
			$outputDir = \trim(\readline("Enter output folder location (Current Folder : $existOpDir): "));
			$this->input->accessKey = $accessToken;
			if ($outputDir != ''){
				$this->input->outputDir = $outputDir;
			}
			$this->complete = true;
		}catch (Exception $e) {
			echo 'Caught exception: ',  $e->getMessage(), "\n";
			$this->complete = false;
		}
	}

	protected function isDone(){
		return $this->complete;
	}

	public function hasChanged(){
		return true;
	}
}

// Authenticator when Dropbox Image Downloader already has account setup
class NoAuthRequired extends DropBoxAuth{
	private $choice='%';
	public function __construct($input, $appInfo){
		parent::__construct($input, $appInfo);
	}

	protected function authenticate(){
		println("\nEnter 'e' to Change Configuration, else press any other character to continue : ");
		do {
			$this->choice = fgetc(STDIN);
		} while ( trim($this->choice) == '' );

		if ($this->choice == 'e'){
			parent::authenticate();
		}
	}
	protected function isDone(){
		if ($this->choice == 'e' || $this->choice == 'E'){
			return parent::isDone();
		}else if ($this->choice == '%'){
			return false;
		}else{
			return true;
		}
	}

	public function hasChanged(){
		if ($this->choice == 'e'){
			return parent::hasChanged();
		}else
		return false;
	}
}

class AuthenticatorFactory{
	public static function getDropBoxAuthenticator($input, $appInfo){
		return new DropBoxAuth($input,$appInfo);
	}
	public static function getNoAuthRequired($input, $appInfo){
		return new NoAuthRequired($input,$appInfo);
	}
}
?>