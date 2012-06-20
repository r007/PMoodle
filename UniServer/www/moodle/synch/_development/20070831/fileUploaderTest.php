<?php
require_once dirname(__FILE__)."/../../admin/synch-setup.php";

echo __FILE__. ' $post = <br /><pre>'. print_r($_POST, true).'</pre></br>';
echo __FILE__. ' $_FILES = <br /><pre>'. print_r($_FILES, true).'</pre></br>';

/*
 * Save the file to disk
 */
 
 //FileSystem::putFileContents();
 //'destination/temp.txt'
 FileSystem::moveFile($_FILES['file1']['tmp_name'], 'destination/'.$_FILES['file1']['name']);

?>