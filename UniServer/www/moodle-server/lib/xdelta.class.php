<?php

/**
 * A class to generate diff's against two file trees
 *
 * The file format used to store the diffs is a zip file cointaining
 * a manifest file describing, new files, deleted files
 * and updated files. Updated files are in a binary diff format.
 *
 */
class xdelta {

    var $patha;
    var $pathb;

    var $treea;
    var $treeb;

    var $version = 1.0;


/// Public functions


    /**
     * Create a diff package from paths a and b
     *
     * @param string $patha Path to diff from
     * @param string $pathb Path to diff to
     * @param string $outputfilename Path to write diff package
     * @return bool
     */
    function create_diff($patha, $pathb, $outputfilename) {
        
        global $CFG;
        $incremental_config = backup_get_config(); //get path to xdelta
        $xdelta = $incremental_config->backup_inc_pathtoxdelta;
        //check for relative path first.
        if (!empty($xdelta) && strpos($xdelta, '..') !==false) { 
            //this is a relative path - convert it before using it!
            $xdelta = realpath($CFG->dataroot.$xdelta);
        }

        $this->patha = $patha;
        $this->pathb = $pathb;

        $this->treea = $this->get_dir_tree($this->patha);
        ksort($this->treea);
        $this->treeb = $this->get_dir_tree($this->pathb);
        ksort($this->treeb);

        $srcchecksum = md5(serialize($this->treea));
        $dstchecksum = md5(serialize($this->treeb));

        $diff = $this->diff_trees();

        $temppath = $this->get_temp_dir();

        if (!is_dir($temppath)) {
            mkdir($temppath);
        }

        $filespath = $temppath.'/files';
        if (!is_dir($filespath)) {
            mkdir($filespath);
        }


        if (!empty($diff['added'])) {
            foreach($diff['added'] as $filename => $type) {

                if ($type === 'directory') {
                    continue;
                }
                $newdir = dirname($filename);
                if (!is_dir($filespath.$newdir)) {
                    mkdir($filespath.$newdir, 0700, true);
                }
                copy($this->pathb.'/'.$filename, $filespath.$filename);
            }
        }

        if (!empty($diff['different'])) {
            foreach($diff['different'] as $filename => $dontcare) {

                // make the path to the file if it doesn't exist
                $newdir = dirname($filename);
                if (!is_dir($filespath.$newdir)) {
                    mkdir($filespath.$newdir, 0700, true);
                }
                //clean up good ole windows paths.
                $xdelta = str_replace('\\','/',$xdelta);
                $xdeltareturn = true; //if true xdelta should return something when it succeeds.

                if(stripos($xdelta, 'xdelta3')) {
                    //this is a xdelta3 box - so use this command
                    $command = ' -e -s ';
                    $xdeltareturn = false; //for some reason xdelta3 on windows returns nothing if it's succesful, returns "something" if it fails!
                } else {
                    //this is an xdelta 1.x box! use this command.
                    $command = ' delta ';
                }
                if (stripos($_SERVER['SERVER_SOFTWARE'], 'win')) {
                    //this is a windows box
                    
                    $xdelta = escapeshellcmd($xdelta);
                    $filea = escapeshellcmd($this->patha.$filename);
                    $fileb = escapeshellcmd($this->pathb.$filename);
                    $filediff = escapeshellcmd($filespath.$filename.'.diff');

                    $batchcommand = '"'.$xdelta.'"'.$command.'"'.$filea.'" "'.$fileb.'" "'.$filediff.'"';                    
                    debugging($batchcommand, DEBUG_DEVELOPER);
                    //system() call has a bug that only allows 2 " in the command. - we need 8 in case windows dirs have spaces - so write the command out to a batch file and then call it.
                    $tmpnam = tempnam($CFG->dataroot.'/temp', "temp").".bat";
                    $fp = fopen ($tmpnam, "w");
                    fwrite($fp, $batchcommand);
                    fclose ($fp);
                    system('"'.$tmpnam.'"', $return);
                    unlink($tmpnam);
                    
                } else {
                    //this is a linux box.
                    system(escapeshellcmd($xdelta.$command.$this->patha.$filename.' '.$this->pathb.$filename.' '.$filespath.$filename.'.diff'), $return);
                }

                if ($xdeltareturn && !$return) {
                    return($this->error('Fail'));
                } elseif (!$xdeltareturn && $return) {
                    debugging($return);
                    return($this->error('Fail'));
                }
            }
        }

        $diff['srcchecksum'] = $srcchecksum;
        $diff['dstchecksum'] = $dstchecksum;
        $diff['version']     = $this->version;

        if ($srcchecksum == $dstchecksum) {
         //this diff hasn't changed. - kill it!
            remove_dir($temppath);
            return($this->error('coursenotchanged'));            
        } else {
            // Write out the manifest.
            if (!$fp = fopen($temppath.'/Manifest', 'w')) {
                return($this->error('Could\'t open Manifest'));
            }
            fwrite($fp, serialize($diff));
            fclose($fp);


            if (!$this->zip_dir($temppath, $outputfilename)) {
                return($this->error('Counld\'t zip up package'));
            }

            remove_dir($temppath);
        }
        return(true);
    }



    /**
     * Apply a diff package against a file tree
     *
     * @param string $basepath Path to apply diff to
     * @param string $diff Path to diff package
     * @return bool
     */
    function apply_diff($basepath, $diff) {
        global $CFG, $COURSE;
        
        $xdelta = new xdelta;
        $incremental_config = backup_get_config(); //get path to xdelta
        $xdeltacmd = $incremental_config->backup_inc_pathtoxdelta;
        if (strpos($xdeltacmd, '..') !==false) { 
            //this is a relative path - convert it before using it!
            $xdeltacmd = realpath($CFG->dataroot.$xdeltacmd);
        }
        
        $temppath = $this->get_temp_dir();
        $filespath = $temppath.'/files';

        // Unpack diff into temp directory
        if (!unzip_file($diff, $temppath, false)) {
            return($this->error('Couldn\'t unzip package'));
        }

        // Restore Manifest
        if (!$serialmanifest = file_get_contents($temppath.'/Manifest')) {
            return($this->error('Couldn\'t open manifest'));
        }

        if (!$manifest = unserialize($serialmanifest)) {
            return($this->error('Couldn\'t unserializse the manifest'));
        }

        if ($manifest['version'] != $this->version) {
            return($this->error('Diff package was generated with xdelta class version '.$manifest['version']));
        }
        // Get base tree
        $this->treea = $this->get_dir_tree($basepath);
        ksort($this->treea);

        // Check src tree against diff checksum
        if ($manifest['srcchecksum'] != md5(serialize($this->treea))) {
            return($this->error('Source file tree is different to that expected in diff'));
        }

        // Copy new files to base path
        if (isset($manifest['added'])) {
            foreach($manifest['added'] as $newfile => $type) {
                if ($type === 'directory') {
                    if (!is_dir($basepath.$newfile)) {
                        mkdir($basepath.$newfile, 0700, true);
                    }
                } else {
                    copy($filespath.$newfile, $basepath.$newfile);
                }
            }
        }

        // Removed deleted files
        if (isset($manifest['deleted'])) {
            krsort($manifest['deleted']);
            foreach($manifest['deleted'] as $deletedfile => $type) {
                fulldelete($basepath.$deletedfile); //delete from new backup.
                if (strpos($deletedfile, 'course_files')) {
                    //need to delete from course as well.
                    $deletedfile = str_replace('/course_files', '', $deletedfile); //strip out course_files
                    if (file_exists($CFG->dataroot.'/'.$COURSE->id.$deletedfile)) {
                        unlink($CFG->dataroot.'/'.$COURSE->id.$deletedfile);
                    }
                }   
            }
        }

        //clean up good ole windows paths.
        $xdeltacmd = str_replace("\\","/",$xdeltacmd);

        if(stripos($xdeltacmd, 'xdelta3')) {
            //this is an xdelta3 command - so use this
            $command = ' -d -s ';
        } else {
            $command = ' patch ';
        }
        if (stripos($_SERVER['SERVER_SOFTWARE'], 'win')) {
            $xdeltacmd = escapeshellcmd($xdeltacmd);
            // Apply diffs against modified files
            if (isset($manifest['different'])) {
                foreach($manifest['different'] as $different => $checksum) {

                    $filea = escapeshellcmd($filespath.$different.'.diff');
                    $fileb = escapeshellcmd($basepath.$different);
                    $filediff = escapeshellcmd($basepath.$different.'.undiff');

                    $batchcommand = '"'.$xdeltacmd.'"'.$command.'"'.$fileb.'" "'.$filea.'" "'.$filediff.'"';                    
                    //system() call has a bug that only allows 2 " in the command. - we need 8 in case windows dirs have spaces - so write the command out to a batch file and then call it.
                    $tmpnam = tempnam($CFG->dataroot.'/temp', "temp").".bat";
                    $fp = fopen ($tmpnam, "w");
                    fwrite($fp, $batchcommand);
                    fclose ($fp);
                    system('"'.$tmpnam.'"', $return);
                    unlink($tmpnam);
                    unlink($basepath.$different);
                    if (!file_exists($basepath.$different.'.undiff')) {
                        return($this->error('Do you have the same XDELTA version as the server?'));
                    }
                    unlink($filespath.$different.'.diff');
                    rename($basepath.$different.'.undiff', $basepath.$different);
                }
            }
        } else {
            //this is a linux box so do this:
            // Apply diffs against modified files
            if (isset($manifest['different'])) {
              foreach($manifest['different'] as $different => $checksum) {
                  system(escapeshellcmd($xdeltacmd.$command.$filespath.$different.'.diff '.$basepath.$different.' '.$basepath.$different.'.undiff'), $return);
                  unlink($filespath.$different.'.diff');
                  rename($basepath.$different.'.undiff', $basepath.$different);
              }
           }

        }

        // Checksum final result
        $this->treeb = $this->get_dir_tree($basepath);
        ksort($this->treeb);

        if ($manifest['dstchecksum'] != md5(serialize($this->treeb))) {
            return($this->error('Dest file tree is different to that expected in diff'));
        }

        remove_dir($temppath);

        return(true);
    }






/// Helper functions from this point on


    /**
     * Get an array of paths in a file tree recursively
     *
     * @param string $path Path to get files from
     * @param string $basepath Used for recursion
     * @param array $files Used for recursion
     * @return array Keyed on file paths
     */
    function get_dir_tree($path, $basepath = '', &$files=array()) {
        $dir = opendir($path);
        if (!$basepath) {
            $basepath = $path;
        }

        while (($file = readdir($dir)) !== false) {
            if ($file == '.' || $file == '..') {
                continue;
            }

            if (is_dir($path.'/'.$file)) {
                $this->get_dir_tree($path.'/'.$file, $basepath, $files);
            }
            $files[substr($path, strlen($basepath), strlen($path)).'/'.$file] = is_file($path.'/'.$file) ? md5_file($path.'/'.$file) : 'directory';
        }
        return($files);
    }


    /**
     * Compares $this->treea and $this->treeb and retuns an
     * array of added,deleted and modified files
     *
     * @return array
     */
    function diff_trees() {

        $a = $this->treea;
        $b = $this->treeb;
        $return = array();

        // find additions
        foreach($b as $path => $type) {
            if (!isset($a[$path])) {
                $return['added'][$path] = $type == 'directory' ? 'directory' : true;
            }
        }
        if (!empty($return['added'])) {
            ksort($return['added']);
        }

        // find deletions
        foreach($a as $path => $dontcare) {
            if (!isset($b[$path])) {
                $return['deleted'][$path] = true;
            }
        }
        if (!empty($return['deleted'])) {
            ksort($return['deleted']);
        }


        // diff reamining files
        foreach($b as $path => $status) {
            if (isset($a[$path]) && isset($b[$path])) {
                if ($a[$path] != $b[$path]) {
                    $return['different'][$path] = true;
                }
            }
        }
        if (!empty($return['different'])) {
            ksort($return['different']);
        }

        return($return);
    }


    /**
     * Get a temporary working directory
     *
     * @return string
     */
    function get_temp_dir() {
        global $CFG;
        
        $tempdir = $CFG->dataroot.'/temp/'.md5(uniqid(rand(), true));
        mkdir($tempdir, 0700, true);
        return($tempdir);
    }

    function zip_dir($basedir, $destfile) {
        $paths = '';

        $dir = opendir($basedir);
        while (false !== ($file=readdir($dir))) {
            if ($file=="." || $file=="..") {
                continue;
            }
            $paths[$file] = $basedir.'/'.$file;
        }

        closedir($dir);

        return(zip_files($paths, $destfile));
    }


    function error($error) {
        $this->error = $error;
        return(false);
    }
}

?>