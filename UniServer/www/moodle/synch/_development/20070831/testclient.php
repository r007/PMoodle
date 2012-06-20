<?php
/**
 * A template to test Moodle's XML-RPC feature
 * 
 * This script 'remotely' executes the mnet_concatenate_strings function in 
 * mnet/testlib.php
 * It steps through each stage of the process, printing some data as it goes
 * along. It should help you to get your remote method working.
 * 
 * @author  Donal McMullan  donal@catalyst.net.nz
 * @version 0.0.1
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package mnet
 */
//require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once dirname(__FILE__)."/../../admin/synch-setup.php";
//require_once dirname(__FILE__).'/fileUploader.php';

GLOBAL $Out;
$Out->append("testing");

error_reporting(E_ALL);
$page = new object;
$page->action = synch_GetRequestItem("action");
$page->self = $_SERVER['PHP_SELF'];
$page->message = null;

switch($page->action){
	case 1;// upload file
		//require_once("fileUploader.php");
		//$filePath = "D:\\Program Files\\Apache\\htdocs\\test.txt";
		//$filePath = 'C:\\projects\\VLE\\Mobile Moodle\\htdocs\\development\\client-1.9\\synch\\_development\\20070831\\test.txt';
		//$filePath = 'source/test.txt';
		//$filePath = realpath('source/test.txt');
		$filePath = realpath('source/backup-cf101-20070806-1529.zip');
		//$filePath = realpath('source/Mobile Moodle Demonstration Version 1.zip');
		
		
		$Out->append("$filePath = ".$filePath);
		$Out->flush();
		//$domain = 'localhost';
		$domain = 'pclt1048.open.ac.uk';
		$instance = 'client-1.9';
		//$instance = 'server-1.9';
		$url = 'http://'.$domain.'/offline/development/'.$instance.'/synch/_development/20070831/fileUploaderTest.php';
		//$url = 'http://cc5983.vledev.open.ac.uk/offline/development/20070903/fileUploaderTest.php';
		$objCurlFileUploader = new CurlFileUploader($filePath, $url,'file1');
		$objCurlFileUploader->UploadFile();
		
		// upload file with some post params
		/*
		$objCurlFileUploader = new CurlFileUploader("D:\\Program Files\\Apache\\htdocs\\test.txt", "http://localhost/fileUploaderTest.php",'file1', Array('test' => 'test1'));
		$objCurlFileUploader->UploadFile();
		*/
		break;
	
}

$Out->flush()
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-US" lang="en-US">
	<head>
		<title>Moodle cUrl test client</title>
	</head>
	<body>
	    <p>
	    Choose a function to call:<br />
	    <a href="<?php echo $page->self?>?action=1">Upload file</a><br />

	</body>
</html>
<?php require_once dirname(__FILE__)."/../../admin/synch-teardown.php";?>