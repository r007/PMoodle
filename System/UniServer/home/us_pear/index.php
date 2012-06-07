<?php
# *****************************************************************
# Uniform Server - 1-3-09
# Gets and uses absoute path $path
# V2 14-1-2011 - Addded new tracking section
# *****************************************************************

//=================================================================
// === Get paths ===
  $path_array      = explode("\\home",dirname(__FILE__)); // Split at folder us_lear
  $us_pear_folder  = $path_array[0]."\home\us_pear";      // Path to us_pear

// After upgrading may get this SECURITY ERROR: Will not write to..- Possible symlink attack
// Solution is to clear the Pear cache.
// At the same time might as well perform other clean up tasks.

//== Folder folder paths

$us_pear_cache         = $us_pear_folder.'\pear\cache';    //Cache
$us_pear_temp_download = $us_pear_folder.'\temp\download'; //Temp

//--Clean Cache folder
if(is_dir($us_pear_cache)){                       //Does folder exist?
  $handle=opendir($us_pear_cache);                //Yes get handle
    while (($file = readdir($handle))!==false) {  //Scan for files
    //echo "$file <br>"; //***Test code
    @unlink($us_pear_cache.'/'.$file);            //and delete file
  }
  closedir($handle);
}

//--Clean Temp folder
if(is_dir($us_pear_temp_download)){               //Does folder exist?
  $handle=opendir($us_pear_temp_download);        //Yes get handle
  while (($file = readdir($handle))!==false) {    //Scan for files
    //echo "$file <br>";//***Test code
    @unlink($us_pear_temp_download.'/'.$file);    //and delete file
  }
closedir($handle);
}

//=== Update config paths
// Portability requires configuration paths to be updated.
// Pear configuration is stored in a file. Its data has been serialised
// this needs to be unspecialised; paths updated and file resaved.

//--Read config file into array
 $filearray=file('pear.conf') ;       // Read file into array
                                      // [0] = Comment line [1]=  serialize string

 $arr = unserialize($filearray[1]);   // unserialize line save to array

//print "<pre>";  //***Test code
//var_dump($arr); //***Test code
//print "</pre>"; //***Test code

//---Set new values
$arr['temp_dir']      = $us_pear_folder.'\temp';
$arr['bin_dir']       = $us_pear_folder;
$arr['php_dir']       = $us_pear_folder.'\PEAR';
$arr['doc_dir']       = $us_pear_folder.'\PEAR\docs';
$arr['data_dir']      = $us_pear_folder.'\PEAR\data';
$arr['test_dir']      = $us_pear_folder.'\PEAR\tests';
$arr['cache_dir']     = $us_pear_folder.'\PEAR\cache'; 
$arr['php_bin']       = $us_pear_folder."\\";
$arr['download_dir']  = $us_pear_folder.'\temp\download';
$arr['php_ini']       = $path_array[0].'\usr\local\php';
$arr['www_dir']       = $us_pear_folder.'\www';
$arr['cfg_dir']       = $us_pear_folder.'\cfg';


//---Serialize and save to array element
$filearray[1] = serialize($arr); 

//--Save array to file - Paths now updated
file_put_contents('pear.conf', $filearray);

//=================================================================


/**
 * Put this file in a web-accessible directory as index.php (or similar)
 * and point your webbrowser to it.
 */

$path = realpath('');    // current absolute path 

// $pear_dir must point to a valid PEAR install (=contains PEAR.php)
$pear_dir = $path.'\PEAR'; // default of install

// OPTIONAL: If you have a config file at a non-standard location,
// uncomment and supply it here:
$pear_user_config = $path.'\pear.conf';

// OPTIONAL: If you have protected this webfrontend with a password in a
// custom way, then uncomment to disable the 'not protected' warning:
$pear_frontweb_protected = true; // localhost or .htaccess protextion 

/***********************************************************
 * Following code tests $pear_dir and loads the webfrontend:
 */
if (!file_exists($pear_dir.'/PEAR.php')) {
    trigger_error('No PEAR.php in supplied PEAR directory: '.$pear_dir,
                    E_USER_ERROR);
}
ini_set('include_path', $pear_dir);
require_once('PEAR.php');

// Include WebInstaller
putenv('PHP_PEAR_INSTALL_DIR='.$pear_dir); // needed if unexisting config
require_once('pearfrontendweb.php');
?>
