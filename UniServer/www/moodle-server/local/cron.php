<?php // $Id$
        //Execute incrementals backup's cron
        //Perhaps a long time and memory could help in large sites
        @set_time_limit(0);
        @raise_memory_limit("192M");
        if (function_exists('apache_child_terminate')) {
            // if we are running from Apache, give httpd a hint that 
            // it can recycle the process after it's done. Apache's 
            // memory management is truly awful but we can help it.
            @apache_child_terminate();
        }
        if (file_exists("$CFG->dirroot/backup/backup_sch_incremental.php") and
            file_exists("$CFG->dirroot/backup/backuplib.php") and
            file_exists("$CFG->dirroot/backup/lib.php") and
            file_exists("$CFG->libdir/blocklib.php")) {
            include_once("$CFG->dirroot/backup/backup_sch_incremental.php");
            include_once("$CFG->dirroot/backup/backuplib.php");
            include_once("$CFG->dirroot/backup/lib.php");
            require_once ("$CFG->libdir/blocklib.php");
            mtrace("Running incrementals if required...");
    
            if (! schedule_incr_backup_cron()) {
                mtrace("ERROR: Something went wrong while performing incremental backup tasks!!!");
            } else {
                mtrace("incremental tasks finished.");
            }
        }

    mtrace("incremental Cron completed");

?>
