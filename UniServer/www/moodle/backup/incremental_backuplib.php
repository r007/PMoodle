<?php

function create_incremental($courseid, $oldbackup, $newbackup) {
    global $CFG;
    $incremental_config = backup_get_config();

    //set directory paths
    if (!empty($incremental_config->backup_inc_destination)) {
        $backuppath = $incremental_config->backup_inc_destination.'/'.$courseid.'/';
    } else {
        $backuppath = $CFG->dataroot.'/'.$courseid.'/backupdata/';
    }
    $temppath = $CFG->dataroot.'/temp/incrementals/'.$courseid . '/';
    $newtempdir = str_ireplace('backup-', '', str_ireplace('.zip', '', $newbackup)); //strip out .zip and backup from the string.
    $oldtempdir = str_ireplace('backup-', '', str_ireplace('.zip', '', $oldbackup));    
      
    if (!check_dir_exists($temppath.$newtempdir, true, true)) { //now create folder for newtempdir
        error('failed to create temp dir for new backup'.$temppath.$newtempdir);
    }
    if (!check_dir_exists($temppath.$oldtempdir, true, true)) { //now create folder for newtempdir
        error('failed to create temp dir for old backup'.$temppath.$oldtempdir);
    }      
    //create incremental directory if doesn't exist
    if (!check_dir_exists($backuppath.'incrementals', true, true)){   
        error('failed to create incrementals directory');
    }       
    //unzip each backup file into the temp dirs   
    if (!unzip_file($backuppath.$oldbackup, $temppath.$oldtempdir, false)) {
        error('Couldn\'t unzip old backup');
    }
    if (!unzip_file($backuppath.$newbackup, $temppath.$newtempdir, false)) {
        error('Couldn\'t unzip new backup');
    }        
    
    //now run the compare against both and create the diff.
    $mydiff = new xdelta;
    $mydiff->create_diff($temppath.$oldtempdir, $temppath.$newtempdir, $backuppath.'incrementals/'.md5($oldbackup).'-'.md5($newbackup).'.zip');
    if (isset($mydiff->error)) {
        if ($mydiff->error=='coursenotchanged') {
            //this course has not changed since the last backup so don't generate any more incrementals - and return false so that the $newbackup can be deleted.
            remove_dir($temppath.$oldtempdir);
            remove_dir($temppath.$newtempdir);
            return false;
        } else {
            print_object($mydiff);
            error("XDELTA diff failed!!- olddir:".$temppath.$oldtempdir. "<br>Newdir:".$temppath.$newtempdir."<br>Backuppath:".$backuppath.'incrementals/'.md5($oldbackup).'-'.md5($newbackup).'.zip');
        }
    }
    //now delete the tmp directories.
    remove_dir($temppath.$oldtempdir);
    remove_dir($temppath.$newtempdir);
    
    return true;

}


function get_list_backups($courseid, $incrementals=false) {
    global $CFG;
    
     $incremental_config = backup_get_config();

    //set directory paths
    if (!empty($incremental_config->backup_inc_destination)) {
        $backuppath = $incremental_config->backup_inc_destination.'/'.$courseid.'/';
    } else {
        $backuppath = $CFG->dataroot.'/'.$courseid.'/backupdata/';
    }
      
    if ($incrementals) {
        $backuppath .= 'incrementals/';
        $backuplist = get_directory_list($backuppath);
    } else {
        $backuplist = get_directory_list($backuppath, 'incrementals');
    }
    //remove any files that aren't zip files.
    foreach ($backuplist as $id => $file) {
        if (!strpos($file, '.zip')) {
            unset($backuplist[$id]);
        }
    }
    rsort($backuplist);
    return $backuplist;
}


function generate_incrementals($courseid) {
    global $CFG;

     //check XDELTA is installed.
     if (!check_xdelta_installed()) {
        mtrace(get_string('xdeltanotinstalled','local'));
        return false;
     }
    
    $incremental_config = backup_get_config();

    //set directory paths
    if (!empty($incremental_config->backup_inc_destination)) {
        $backuppath = $incremental_config->backup_inc_destination.'/'.$courseid.'/';
    } else {
        $backuppath = $CFG->dataroot.'/'.$courseid.'/backupdata/';
    }
    
    $backuplist = get_list_backups($courseid);

    $latestbackup = $backuplist[0];
     foreach($backuplist as $id => $backup) {
         if ($id <> 0) {
            if (!create_incremental($courseid, $backup, $latestbackup)) {
                //don't generate any more incrementals as the $backup is the same as $latestbackup and we don't need to keep this backup at all.
                unlink($backuppath.$latestbackup); //delete new backup as it isn't needed.                
                mtrace(get_string('coursenotchanged', 'local'));
                break; //stop running more incrementals.
            }
         }  
     }
     //now cleanup incremental files that are no longer needed.
     $incrementallist = get_list_backups($courseid, true);

     foreach ($incrementallist as $incremental) {
        $findstr = strpos($incremental, md5($latestbackup));
         if (!$findstr) {
             unlink($backuppath.'incrementals/'.$incremental);
         }
     }
     return true;
}

function get_incremental($currenthash) {
    global $CFG;
    $incremental_config = backup_get_config();
    $crnt = get_record('incremental_instance', 'hash', $currenthash);
    if (!empty($crnt)) {
        $crnt_course = get_records('incremental_instance', 'courseid', $crnt->courseid, 'timecreated');
        //print_object($crnt_course);
        $newbkup = array_pop($crnt_course); //get latest course hash.
        while ($newbkup->hash == $newbkup->filename) { //make sure the record is valid - if both hash and filename are exact - this is related to a client update - not the server backup.
            $newbkup = array_pop($crnt_course); //get latest course hash.
        }
        $returnfile = new object();

        if ($currenthash ==  $newbkup->hash) {
            $returnfile->name = 'uptodate';
            return $returnfile;
        }
        
        //set directory paths
        if (!empty($incremental_config->backup_inc_destination)) {
            $backuppath = $incremental_config->backup_inc_destination.'/'.$crnt->courseid.'/';
        } else {
            $backuppath = $CFG->dataroot.'/'.$crnt->courseid.'/backupdata/';
        }
        
        
        //now check to see if an incremental is available for the $currenthash
        if (file_exists($backuppath.'incrementals/'.$currenthash.'-'.$newbkup->hash.'.zip')) {
            $returnfile->name = $currenthash.'-'.$newbkup->hash.'.zip';
            $returnfile->path = $backuppath.'incrementals/';
        } else { 
            $returnfile->name = $newbkup->filename;
            //incremental doesn't exist so send a full backup file instead.
        }
        return $returnfile;
    } else {
        return false;
        
    }
}

function apply_incremental($courseid, $newfile) {
    global $CFG;
    //first need to get the "current" version of the backup. - we'll do this based on the incremental passed.

    $newfilename = substr($newfile, strrpos($newfile, '/')+1);
    $hash = explode('-', $newfilename);

    $oldbackuppath = $CFG->dataroot.'/incrementals_client/'.$courseid.'/';
    if (file_exists($oldbackuppath.$hash[0].'.zip')) {
        //now unzip original backup into a temp dir.
        $tempdir = $CFG->dataroot.'/temp/'.$hash[0].'-'.uniqid(); //add uniqid to the end as for some stupid reason, when unzip_file unzips over the top of an existing dir - it changes the md5 of the moodle.xml file!
        if (!check_dir_exists($tempdir, true, true)) {
            error("failed to create temp dir for original backup");
        }
        if (!unzip_file($oldbackuppath.$hash[0].'.zip', $tempdir, false)) {
            error('Couldn\'t unzip old backup');
        }
        //now run diff!
        $mydiff = new xdelta;
        $mydiff->apply_diff($tempdir, $newfile);
        if (!empty($mydiff->error)) {
           error("apply diff failed: ".$mydiff->error);
        }
        //now zip up the dir again, 
        $listoffiles = get_directory_list($tempdir, '',true, true);
        $files = array();
        foreach ($listoffiles as $file) {
           $files[] = "$tempdir/$file";
        }

        if (!zip_files($files, $oldbackuppath.$hash[1])) {
            error('failed to zip files after diff');
        }
        return $hash[1];
        //now return path to new backup.
    } else {
        error("Could not find the original backup for this course - you must download a full backup oldpath:".$oldbackuppath.' hash:'.$hash[0].'.zip');
    }

}

function download_incremental($currenthash, $courseid){
    global $CFG;

    if (empty($currenthash)) {
        error("current hash not provided!");
    }
    //get update server from config_plugins
    $backupserver = get_field('config_plugins', 'value', 'name', 'backup_inc_server');
    if (!empty($backupserver)) {
        $externalurl = $backupserver.'?hash='.$currenthash;
    } else {
        error("update cannot continue - you must specify an update server in admin > Courses > Incremental Client");
    }
    $newfilepath = $CFG->dataroot.'/incrementals_client/'.$courseid.'/diff/';
    if (!check_dir_exists($newfilepath, true, true)) {
        error("cant create incremental directory");
    }
 
    //first find out if we can connect to the remote site and if there is an incremental available.
    $resp = download_file_content($externalurl.'&action=check');
        
    //now do some checking on $resp - it should return the filename for the file we are going to get, otherwise, an error has occured.

    if ($resp <> clean_filename($resp)) { //make sure the value returned looks like a filename!
        return 'autofailed'; // automatic update has failed - send the user to a manual update form.
    } elseif ($resp == 'uptodate') {
        return 'uptodate';
    } else {
        //now get the incremental! 
        $newfile = $newfilepath.$resp;
        $err_msg = '';
        $fileresp = download_file_content($externalurl.'&action=curldownload', null, null, false, 300, 20, false, $newfile);
        if (!file_exists($newfile)) {
            print_object($fileresp);
            error("failed to get incremental!!!");
        }
        //now we've downloaded and saved the new incremental - do something with it!
        $pos = strpos($resp, '-'); // incrementals will have this char in the filename, full backups won't.
        if ($pos === false) { //this is a full backup file.
            //this is a full backup file, so don't run apply incremental. - just return the link to the file.
            return $newfile;
        } else {
            return apply_incremental($courseid, $newfile);          
        }      
    }
}
//displays a manual download form.
//$id = course id
function incremental_manual_download_form($id) {
    global $CFG;

    echo '<div style="text-align:center">';
    echo '<form enctype="multipart/form-data" method="post" action="upload_incremental.php">';
    echo '<fieldset class="invisiblefieldset">';
    echo '<input type="hidden" name="id" value="'.$id.'" />';
    echo '<input type="hidden" name="action" value="uploadfile" />';
    require_once($CFG->libdir.'/uploadlib.php');
    upload_print_form_fragment(1,array('newfile'),null,false,null,0,null,false);
    echo '<input type="submit" name="save" value="'.get_string('restorethisfile', 'local').'" />';
    echo '</fieldset>';
    echo '</form>';
    echo '</div>';
    echo '<br />';

}
//function to check to see if Xdelta is installed.,
function check_xdelta_installed() {
    global $CFG;
    $xdeltacmd = get_field('backup_config', 'value', 'name', 'backup_inc_pathtoxdelta');
    //check for relative path first.
    if (!empty($xdeltacmd) && strpos($xdeltacmd, '..') !==false) { 
        //this is a relative path - convert it before using it!
        $xdeltacmd = realpath($CFG->dataroot.$xdeltacmd);
    }

    if(empty($xdeltacmd) or !file_exists($xdeltacmd)) {
        return false;
    }
    return true;
}
?>