<?php

namespace ParseCloud;

class parseRestClient{

        const APPID = 'W84ve2WEofX0DZixWm2ibeLC33Zpvh0sQobXfyMR';
        const MASTERKEY = 'gx9d5R9QSpWf2K6UA2siBPCzSkEuEww38Bch7Ane';
        const RESTKEY = 'TMDG338Rkd9Oh7pNKoJGW8vnEQrE3GJGvfKzDEgo';
        const PARSEURL = 'https://api.parse.com/1/';
    
	private $_appid = '';
	private $_masterkey = '';
	private $_restkey = '';
	private $_parseurl = '';

	public $data;
	public $requestUrl = '';
	public $returnData = '';

	public function __construct(){
                
		$this->_appid = self::APPID;
                $this->_masterkey = self::MASTERKEY;
                $this->_restkey = self::RESTKEY;
                $this->_parseurl = self::PARSEURL;

		if(empty($this->_appid) || empty($this->_restkey) || empty($this->_masterkey)){
			$this->throwError('You must set your Application ID, Master Key and REST API Key');
		}

		$version = curl_version();
		$ssl_supported = ( $version['features'] & CURL_VERSION_SSL );

		if(!$ssl_supported){
			$this->throwError('CURL ssl support not found');	
		}

	}

	/*
	 * All requests go through this function
	 * 
	 *
	 */	
	public function request($args){
		$isFile = false;
		$c = curl_init();
		curl_setopt($c, CURLOPT_TIMEOUT, 30);
		curl_setopt($c, CURLOPT_USERAGENT, 'parse.com-php-library/2.0');
		curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($c, CURLINFO_HEADER_OUT, true);
		if(substr($args['requestUrl'],0,5) == 'files'){
			curl_setopt($c, CURLOPT_HTTPHEADER, array(
				'Content-Type: '.$args['contentType'],
				'X-Parse-Application-Id: '.$this->_appid,
				'X-Parse-Master-Key: '.$this->_masterkey
			));
			$isFile = true;
		}
		else if(substr($args['requestUrl'],0,5) == 'users' && isset($args['sessionToken'])){
			curl_setopt($c, CURLOPT_HTTPHEADER, array(
    			'Content-Type: application/json',
    			'X-Parse-Application-Id: '.$this->_appid,
    			'X-Parse-REST-API-Key: '.$this->_restkey,
    			'X-Parse-Session-Token: '.$args['sessionToken']
    		));
		}
		else{
			curl_setopt($c, CURLOPT_HTTPHEADER, array(
				'Content-Type: application/json',
				'X-Parse-Application-Id: '.$this->_appid,
				'X-Parse-REST-API-Key: '.$this->_restkey,
				'X-Parse-Master-Key: '.$this->_masterkey
			));	
		}
		curl_setopt($c, CURLOPT_CUSTOMREQUEST, $args['method']);
		$url = $this->_parseurl . $args['requestUrl'];

		if($args['method'] == 'PUT' || $args['method'] == 'POST'){
			if($isFile){
				$postData = $args['data'];
			}
			else{
				$postData = json_encode($args['data']);
			}
			
			curl_setopt($c, CURLOPT_POSTFIELDS, $postData );
		}

		if($args['requestUrl'] == 'login'){
			$urlParams = http_build_query($args['data'], '', '&');
			$url = $url.'?'.$urlParams;
		}
		if(array_key_exists('urlParams',$args)){
			$urlParams = http_build_query($args['urlParams'], '', '&');
    		$url = $url.'?'.$urlParams;
		}

		curl_setopt($c, CURLOPT_URL, $url);

		$response = curl_exec($c);
		$responseCode = curl_getinfo($c, CURLINFO_HTTP_CODE);

		$expectedCode = array('200');
		if($args['method'] == 'POST' && substr($args['requestUrl'],0,4) != 'push'){
			// checking if it is not cloud code - it returns code 200
			if(substr($args['requestUrl'],0,9) != 'functions'){
				$expectedCode = array('200','201');
			}
		}

		//BELOW HELPS WITH DEBUGGING		
		/*
		if(!in_array($responseCode,$expectedCode)){
			//print_r($response);
			//print_r($args);		
		}
		*/
		return $this->checkResponse($response,$responseCode,$expectedCode);
	}

	public function dataType($type,$params){
		if($type != ''){
			switch($type){
				case 'date':
					$return = array(
						"__type" => "Date",
						"iso" => date("c", strtotime($params))
					);
					break;
				case 'bytes':
					$return = array(
						"__type" => "Bytes",
						"base64" => base64_encode($params)
					);			
					break;
				case 'pointer':
					$return = array(
						"__type" => "Pointer",
						"className" => $params[0],
						"objectId" => $params[1]
					);			
					break;
				case 'geopoint':
					$return = array(
						"__type" => "GeoPoint",
						"latitude" => floatval($params[0]),
						"longitude" => floatval($params[1])
					);			
					break;
				case 'file':
					$return = array(
						"__type" => "File",
						"name" => $params[0],
					);			
					break;
				case 'increment':
					$return = array(
						"__op" => "Increment",
						"amount" => $params[0]
					);
					break;
				case 'decrement':
					$return = array(
						"__op" => "Decrement",
						"amount" => $params[0]
					);
					break;
				default:
					$return = false;
					break;	
			}
			
			return $return;
		}	
	}

	public function throwError($msg,$code=0){
		throw new parseLibraryException($msg,$code);
	}

	private function checkResponse($response,$responseCode,$expectedCode){
		//TODO: Need to also check for response for a correct result from parse.com
		if(!in_array($responseCode,$expectedCode)){
			$error = json_decode($response);
			$this->throwError($error->error,$error->code);
		}
		else{
			//check for empty return
			if($response == '{}'){
				return true;
			}
			else{
				return json_decode($response);
			}
		}
	}
}



?>