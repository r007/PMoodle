<?php //$Id$

class block_incrementalclient extends block_base {

    function init() {
        $this->title = get_string('offlinemoodle', 'local');
        $this->version = 2007101509;
    }

    function applicable_formats() {
        return array('all' => true);
    }

    function specialization() {
        $this->title = get_string('offlinemoodle', 'local');
    }

    function instance_allow_multiple() {
        return false;
    }

    function get_content() {
        global $COURSE, $CFG;
        $filteropt = new stdClass;
        $filteropt->noclean = true;
        $context = get_context_instance(CONTEXT_SYSTEM);

        $this->content = new stdClass;
        $this->content->text = '<img src="'.$CFG->pixpath.'/i/restore.gif" class="icon" alt="" />';
        if ($COURSE->id == SITEID) {
            //show link to create new course
            $this->content->text .= '<a href="backup/get_incremental.php?action=newcourse">New Offline Moodle Course</a><br>';
        } else {
            $this->content->text .= '<a href="'.$CFG->wwwroot.'/backup/get_incremental.php?id='.$COURSE->id.'">Update Course</a><br>';
        }
        
        if ($COURSE->id !== SITEID and has_capability('moodle/site:restore', $context)) {
            $this->content->text .='<img src="'.$CFG->pixpath.'/i/restore.gif" class="icon" alt="" />';
            $this->content->text .='<a href="'.$CFG->wwwroot.'/backup/export_incremental.php?id='.$COURSE->id.'">'.get_string('export', 'local').'</a>';
        }
        
        $this->content->footer = '';

        unset($filteropt); // memory footprint

        return $this->content;
    }

    /**
     * Will be called before an instance of this block is backed up, so that any links in
     * any links in any HTML fields on config can be encoded.
     * @return string
     */
    function get_backup_encoded_config() {
        /// Prevent clone for non configured block instance. Delegate to parent as fallback.
        if (empty($this->config)) {
            return parent::get_backup_encoded_config();
        }
        $data = clone($this->config);
        $data->text = backup_encode_absolute_links($data->text);
        return base64_encode(serialize($data));
    }

    /**
     * This function makes all the necessary calls to {@link restore_decode_content_links_worker()}
     * function in order to decode contents of this block from the backup 
     * format to destination site/course in order to mantain inter-activities 
     * working in the backup/restore process. 
     * 
     * This is called from {@link restore_decode_content_links()} function in the restore process.
     *
     * NOTE: There is no block instance when this method is called.
     *
     * @param object $restore Standard restore object
     * @return boolean
     **/
    function decode_content_links_caller($restore) {
        global $CFG;

        if ($restored_blocks = get_records_select("backup_ids","table_name = 'block_instance' AND backup_code = $restore->backup_unique_code AND new_id > 0", "", "new_id")) {
            $restored_blocks = implode(',', array_keys($restored_blocks));
            $sql = "SELECT bi.*
                      FROM {$CFG->prefix}block_instance bi
                           JOIN {$CFG->prefix}block b ON b.id = bi.blockid
                     WHERE b.name = 'incrementalclient' AND bi.id IN ($restored_blocks)"; 

            if ($instances = get_records_sql($sql)) {
                foreach ($instances as $instance) {
                    $blockobject = block_instance('incrementalclient', $instance);
                    $blockobject->instance_config_commit($blockobject->pinned);
                }
            }
        }

        return true;
    }
}
?>
