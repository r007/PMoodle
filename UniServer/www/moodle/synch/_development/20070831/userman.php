<?php
// upload file
require_once("fileUploader.php");
$objCurlFileUploader = new CurlFileUploader("D:\\Program Files\\Apache\\htdocs\\test.txt", "http://localhost/fileUploaderTest.php",'file1');
$objCurlFileUploader->UploadFile();

// upload file with some post params
$objCurlFileUploader = new CurlFileUploader("D:\\Program Files\\Apache\\htdocs\\test.txt", "http://localhost/fileUploaderTest.php",'file1', Array('test' => 'test1'));
$objCurlFileUploader->UploadFile();
?>