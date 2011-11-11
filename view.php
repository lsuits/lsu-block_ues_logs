<?php

require_once '../../config.php';
require_once 'classes/lib.php';

require_login();

$courseid = required_param('id', PARAM_INT);
$sectionid = optional_param('sectionid', null, PARAM_INT);

$course_params = array('id' => $courseid);

$course = $DB->get_record('course', $course_params);
if (empty($course)) {
    print_error('no_course', 'block_cps_tracking');
}

$context = get_context_instance(CONTEXT_COURSE, $course->id);
if (!has_capability('moodle/grade:edit', $context)) {
    print_error('no_permission', 'block_cps_tracking');
}

$_s = cps::gen_str('block_cps_tracking');

$blockname = $_s('pluginname');
$PAGE->set_url('/blocks/cps_tracking/view.php', $course_params);
$PAGE->set_context($context);
$PAGE->set_course($course);
$PAGE->set_heading($blockname);
$PAGE->set_title($blockname);
$PAGE->navbar->add($blockname);
$PAGE->set_pagetype('block_cps_tracking');

echo $OUTPUT->header();
echo $OUTPUT->heading($blockname);

$sections = cps_section::from_course($course);

$course_label = function ($course, $section) {
    return "$course->department $course->cou_number $section->sec_number";
};

$to_option = function ($section) use ($course_label) {
    $course = $section->course();
    return $course_label($course, $section);
};

//TODO: print out section drop down; filter by capability
$section_selector = array_map($to_option, $sections);

$to_tables = function ($in, $section) use ($_s, $course_label, $OUTPUT) {
    $by_params = array('l.sectionid' => $section->id);

    $action_filter = optional_param('action', null, PARAM_TEXT);
    if ($action_filter) {
        $by_params['l.action'] = $action_filter;
    }

    $firstname_filter = optional_param('fn', null, PARAM_TEXT);
    if ($firstname_filter) {
        $by_params['u.firstname'] = $firstname_filter;
    }

    $lastname_filter = optional_param('ln', null, PARAM_TEXT);
    if ($lastname_filter) {
        $by_params['u.lastname'] = $lastname_filter;
    }

    $order = optional_param('order', null, PARAM_TEXT);
    $by = optional_param('dir', 'DESC', PARAM_TEXT);
    if ($order) {
        $order_by = "$order $by";
    } else {
        $order_by = 'timestamp DESC';
    }

    $logs = cps_log::get_by_special($by_params, $order_by);
    $count = count($logs);

    $n_head = get_string('firstname') . ' / '. get_string('lastname');

    $table = new html_table();
    $table->head = array($n_head, get_string('action'), get_string('time'));
    $table->data = array();

    foreach ($logs as $log) {
        $name = fullname($log);

        $class = $log->action == 'AD' ? 'add' : 'drop';
        $action = '<span class = "table_'.$class.'">' . $log->action . '</span>';

        $line = array($name, $action, date('Y-m-d', $log->timestamp));
        $table->data[] = new html_table_row($line);
    }

    echo $OUTPUT->heading($course_label($section->course(), $section));

    echo '<div class = "tracking_table">' .
            html_writer::table($table) .
         '</div>';

    return $in or !empty($count);
};

if ($sectionid) {
    $sections = cps_section::get_all(array('id' => $sectionid));
}

$success = array_reduce($sections, $to_tables, false);

if (!$success) {
    $OUTPUT->notification($_s('no_logs'));
}

echo $OUTPUT->footer();
