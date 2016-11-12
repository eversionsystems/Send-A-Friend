<?php
/**
 * PassKit API Helper class for PHP
 * @author PassKit
 *
 */
class PassKit {
	
	private $apiKey;
	private $apiSecret;
	// API version
	private $apiVersion = "v1";
	// API base URL
	private $apiUrl = "https://api.passkit.com/";
	
	function __construct($apiKey, $apiSecret) {
		$this->apiKey = $apiKey;
		$this->apiSecret = $apiSecret;
	}
	
	/**
	 * Does the curl call to the PassKit API.
	 * Authorizes via HMAC
	 * 
	 * @param string $path
	 * @param array $data Array with data (if any)
	 * @return Ambigous <boolean, mixed> Returns the API json as an object, or returns false on failure
	 */
	private function doQuery($path, $data = null, $validatePost = true, $auth = "digest") {
		// Prepare URL
		$path = str_replace(" ", "%20", $path);
		$url = $this->apiUrl . $this->apiVersion . '/' . $path;

		// initiate curl
		$ch = curl_init($url);
		
		$method = "GET";
		$dataString = '';
		// If data isset - sort it, and prep the POST array, also build the string for HMAC hashing
		if($data != null) {
			$method = "POST";
			uksort($data, 'strcmp');
			$post = array();
			
			// Loop through every data entry - only do this for non image method
			foreach($data as $key => $value) {
				if ($validatePost && strpos($value, '@') === 0) {
					$value = "\0".$value;
				}
				$post[$key] = $value;
				$dataString .= rawurlencode($key) . '=' . rawurlencode($value) . "&";
			}
			
			$dataString = substr($dataString, 0, -1);
			
			// Set cURL post options
			curl_setopt ($ch, CURLOPT_POST, true);
			curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);
		}

		if($auth != "hmac") {
			// If not default HMAC, then use Digest
			curl_setopt ($ch, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
			curl_setopt ($ch, CURLOPT_USERPWD, $this->apiKey.':'.$this->apiSecret);
		}
		else {
			// Defaults to via HMAC algorithm. The Authorization header needs to have the following format: PKAuth key hash timestamp
			// The hash is: requestMethod\n requestUri (minus / at the end)\n data\n timestamp
			$to_hash = $method . "\n" . trim($this->apiVersion . "/" . $path, '/') . "\n" . $dataString . "\n" . time();
			$hash = base64_encode(hash_hmac('sha256', $to_hash, $this->apiSecret, true));
				
			// Set Authorization string
			$authorizationString = "PKAuth ".$this->apiKey." ".$hash." ".time();
			$headers[0] = "Authorization: ".$authorizationString;
				
			curl_setopt ($ch, CURLOPT_HTTPHEADER, $headers);
		}
		
		// Set cURL options
		
		curl_setopt ($ch, CURLOPT_HEADER, false);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, false); // required for older cURL libraries that don't support TLS
		
		// Execute then close curl connection
		$response = curl_exec($ch);
		
		// If there is a response, then decode it into a JSON object
		$result = ($response ? json_decode($response, true) : false);
		
		return $result;
	}
	
	// /v1/authenticate
	function authenticate() {
		return $this->doQuery("authenticate");
	}
	
	// /v1/template/list
	function listTemplates() {
		return $this->doQuery("template/list");
	}
	
	// /v1/template/{templateName}/fieldnames
	function getTemplateFieldNames($templateName, $full = true) {
		$request = "template/".$templateName."/fieldnames";
		$request .= ($full === true) ? "/full" : "";
		
		return $this->doQuery($request);
	}
	
	// /v1/template/{templateName}/passes
	function getTemplatePasses($templateName) {
		return $this->doQuery("template/".$templateName."/passes");
	}
	
	// /v1/template/{templateName}/reset
	function resetTemplate($templateName) {
		return $this->doQuery("template/".$templateName."/reset");
	}
	
	// /v1/pass/issue/template/{templateName}
	function issuePass($templateName, $data) {
		return $this->doQuery("pass/issue/template/".$templateName, $data);
	}
	
	// /v1/pass/update/template/{templateName}/serial/{serialNumber} /push
	function updatePassByTemplateSerial($templateName, $serialNumber, $data, $push = true) {
		$request = "pass/update/template/".$templateName."/serial/".$serialNumber;
		$request .= ($push === true) ? "/push" : "";
		return $this->doQuery($request, $data);
	}
	
	// /v1/pass/update/passid/{passId}
	function updatePassById($passId, $data, $push = true) {
		$request = "pass/update/passid/".$passId;
		$request .= ($push === true) ? "/push" : "";
		return $this->doQuery($request, $data);
	}
	
	// /v1/pass/invalidate/template/{templateName}/serial/{serialNumber}
	function invalidatePassByTemplateSerial($templateName, $serialNumber, $data, $push = true) {
		$request = "pass/invalidate/template/".$templateName."/serial/".$serialNumber;
		$request .= ($push === true) ? "/push" : "";
		return $this->doQuery($request, $data);
	}
	
	// /v1/pass/invalidate/passid/{passId}
	function invalidatePassByPassId($passId, $data, $push = true) {
		$request = "pass/invalidate/passid/".$passId;
		$request .= ($push === true) ? "/push" : "";
		return $this->doQuery($request, $data);
	}
	
	// /v1/pass/get/template/{templateName}/serial/{serialNumber}
	function getPassDetailsByTemplateSerial($templateName, $serialNumber) {
		return $this->doQuery("pass/get/template/".$templateName."/serial/".$serialNumber);
	}
	
	// /v1/pass/get/template/{templateName}/select/{query}
	function getPassRecordByQuery($templateName, $query) {
		return $this->doQuery("pass/get/template/".$templateName."/select/".$query);
	}
	
	// /v1/pass/get/passid/{passId}
	function getPassDetailsByPassId($passId) {
		return $this->doQuery("pass/get/passid/".$passId);
	}
	
	// /v1/pass/shareid/{shareId}
	function getPassIdByShareId($shareId) {
		return $this->doQuery("pass/shareid/".$shareId);
	}
	
	// /v1/image/add/{imageType}
	function addImageByLocalFile($imageType, $filename) {
		// Only specific roles are supported
		if(!in_array($imageType, array('background', 'footer', 'icon', 'logo', 'strip', 'thumbnail'))){
			echo 'Invalid image type: '.$imageType;
			return false;
		}
		
		// file must exist
		if(!file_exists($filename)){
			echo 'Image file not found';
			return false;
		}
		
		// only jpg, gif and png are supported
		$filetype = exif_imagetype($filename);
		if(!in_array($filetype,array(IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG))){
			echo 'Image type not supported';
			return false;
		}
		
		// check maximum filesize (1.5 MB)
		if(filesize($filename) > 1572864){
			echo 'File to be big, maximum support is 1.5 MB';
		}
		
		return $this->doQuery('image/add/'.$imageType, array('image' => '@'.$filename.';type=image/'.$filetype), false, "digest");
	}
	
	// /v1/image/add/{imageType} ? /url={url}
	function addImageByURL($imageType, $url) {
		// Only specific roles are supported
		if(!in_array($imageType, array('background', 'footer', 'icon', 'logo', 'strip', 'thumbnail'))){
			echo 'Invalid image type: '.$imageType;
			return false;
		}
		//return $this->doQuery('image/add/'.$imageType.'?url='.$url);
		return $this->doQuery('image/add/'.$imageType.'?url='.$url, null, true, "digest");
	}
	
	// /v1/image/{imageID}
	function getImageDetails($imageId) {
		return $this->doQuery("image/".$imageId);
	}
}