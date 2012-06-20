<?php
/**************************************************************************************
Author:  Abhishek Kumar Srivastava
Email:     abhisheksrivastava@fastmail.fm
Purpose: The class is written for uploading file using curl
Liscense: GNU GPL
**************************************************************************************/

class CurlFileUploader {
	var $filePath;
	var $uploadURL;
	var $formFileVariableName;
	var $postParams = array();
	
	/* Constructor for CurlFileUploader
	* @param $filePath absolute path of file
	* @param $uploadURL url where you want to upload file
	* @param $formFileVariableName form field name to upload file
	* @param $otherParams assosiative array of other params which you want to send as post
	*/ 
	function CurlFileUploader ($filePath, $uploadURL, $formFileVariableName, /* assosiative array */ $otherParams = false) {
		$this->filePath = $filePath;
		$this->uploadURL = $uploadURL;
		if(is_array($otherParams) && $otherParams != false) {
			foreach ($otherParams as $fieldName => $fieldValue) {
				$this->postParams[$fieldName] = $fieldValue;
			}
		}
		$this->postParams[$formFileVariableName] = "@".$filePath;
		
	}
	
	/*
	* function to upload file
	* if unable to upload file produce error and exit
	* else upload file
	*/
	function UploadFile () {
   		GLOBAL $Out;

   		$ch = curl_init();
   		curl_setopt($ch, CURLOPT_URL, $this->uploadURL );
   		curl_setopt($ch, CURLOPT_POST, 1 );
   		$Out->print_r($this->postParams, '$this->postParams = ');

   		//print_r($this->postParams);
   		curl_setopt($ch, CURLOPT_POSTFIELDS, $this->postParams);
   		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   		$postResult = curl_exec($ch);
		
		
		$Out->print_r($postResult, '$postResult = ');
   		if (curl_errno($ch)) {
       		print curl_error($ch);
       		print "<br>Unable to upload file.";
       		exit();
   		}
   		curl_close($ch);
		//exit();
	}
}
?>