<?php // $Id$
      // allows selection of courses to apply the scheduled incrementals.

    require('../config.php');
    require_once($CFG->libdir.'/adminlib.php');

    admin_externalpage_setup('incrementalcourses');

    define("MAX_COURSES_PER_PAGE", 1000);

    $add            = optional_param('add', 0, PARAM_BOOL);
    $remove         = optional_param('remove', 0, PARAM_BOOL);
    $showall        = optional_param('showall', 0, PARAM_BOOL);
    $searchtext     = optional_param('searchtext', '', PARAM_RAW); // search string
    $previoussearch = optional_param('previoussearch', 0, PARAM_BOOL);
    $previoussearch = ($searchtext != '') or ($previoussearch) ? 1:0;

    if (! $site = get_site()) {
        redirect("$CFG->wwwroot/$CFG->admin/index.php");
    }
   //TOTO need to force only admins on this page? - or does the above function do it?


    $strassigncourses = get_string('metaassigncourses');
    $stralreadycourses = get_string('metaalreadycourses');
    $strnoalreadycourses = get_string('metanoalreadycourses');
    $strpotentialcourses = get_string('metapotentialcourses');
    $strnopotentialcourses = get_string('metanopotentialcourses');
    $straddcourses = get_string('metaaddcourse');
    $strremovecourse = get_string('metaremovecourse');
    $strsearch        = get_string("search");
    $strsearchresults  = get_string("searchresults");
    $strcourses   = get_string("courses");
    $strshowall = get_string("showall");


/// Print the header stuff

    admin_externalpage_print_header();

    if (!$frm = data_submitted()) {
        $note = get_string('addincrementalcoursenote', 'local');
        print_simple_box($note, "center", "50%");

/// A form was submitted so process the input

    } else {
        if ($add and !empty($frm->addselect) and confirm_sesskey()) {
            $timestart = $timeend = 0;
            foreach ($frm->addselect as $addcourse) {
                $addcourse = clean_param($addcourse, PARAM_INT);
                set_time_limit(180);
                $increcord = new object();
                $increcord->courseid = $addcourse;
                if (!insert_record('incremental_courses',$increcord)) {
                    error("Could not add the selected course to the incremetals!");
                }
            }
        } else if ($remove and !empty($frm->removeselect) and confirm_sesskey()) {
            foreach ($frm->removeselect as $removecourse) {
                set_time_limit(180);
                //$removecourse = clean_param($removecourse, PARAM_INT);
                //print_object($removecourse);
                if (! delete_records('incremental_courses', 'courseid', $removecourse)) {
                    error("Could not remove the selected course from the incrementals!");
                }
            }
        } else if ($showall and confirm_sesskey()) {
            $searchtext = '';
            $previoussearch = 0;
        }
    }

/// Get all existing students and teachers for this course.
    if(! $alreadycourses = get_records('incremental_courses')) {
        $alreadycourses = array();
    } 
    $acourses = array();
    foreach($alreadycourses as $acourse) {
        //$acourses[$acourse->courseid]->courseid = $acourse->courseid;
        //TODO this should use a nice join and only call the db once instead of for each course in the incrementals.
        $acourses[$acourse->courseid] = get_record('course', 'id', $acourse->courseid);
    }
        $alreadycourses = $acourses;
    
    $numcourses = 0;


/// Get search results excluding any courses already in the incrementals
    if (($searchtext != '') and $previoussearch and confirm_sesskey()) {
        if ($searchcourses = get_courses_search(explode(" ",$searchtext),'fullname ASC',0,99999,$numcourses)) {
            foreach ($searchcourses as $i=>$tmp) {
                if (isset($alreadycourses[$tmp->id])) {
                    unset($searchcourses[$i]);
                }
                if (!empty($tmp->metacourse)) {
                    unset($searchcourses[$i]);
                }
            }
            $numcourses = count($searchcourses);
        }
    }

/// If no search results then get potential students for this course excluding users already in course
    if (empty($searchcourses)) {
        $courses = get_courses('all', 'id','c.id,c.fullname,c.shortname');
        foreach($courses as $i=>$course) {
            if (isset($alreadycourses[$course->id])) {
                unset($courses[$i]);
            }
        }
        $numcourses = count($courses);
    }

    print_simple_box_start("center");

    include('importcourses.html');

    print_simple_box_end();

    admin_externalpage_print_footer();
?>
