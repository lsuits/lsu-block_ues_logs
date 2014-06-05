<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.


/**
 *
 * @package    block_ues_people
 * @copyright  2014 Louisiana State University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once '../../config.php';
require_once 'classes/lib.php';

require_login();

$courseid = required_param('id', PARAM_INT);

// Filters
$sectionid = optional_param('sectionid', null, PARAM_INT);
$action = optional_param('action', null, PARAM_TEXT);
$ln = optional_param('ln', null, PARAM_TEXT);
$fn = optional_param('fn', null, PARAM_TEXT);

// Sorts
$order = optional_param('order', null, PARAM_TEXT);
$by = optional_param('dir', 'DESC', PARAM_TEXT);

$course_params = array('id' => $courseid);

$course = $DB->get_record('course', $course_params);
if (empty($course)) {
    print_error('no_course', 'block_ues_logs');
}

$context = context_course::instance($course->id);
if (!has_capability('moodle/grade:edit', $context)) {
    print_error('no_permission', 'block_ues_logs');
}

$_s = ues::gen_str('block_ues_logs');

$blockname = $_s('pluginname');
$PAGE->set_url('/blocks/ues_logs/view.php', $course_params);
$PAGE->set_context($context);
$PAGE->set_course($course);
$PAGE->set_heading($blockname);
$PAGE->set_title($blockname);
$PAGE->navbar->add($blockname);
$PAGE->set_pagetype('block_ues_logs');

echo $OUTPUT->header();
echo $OUTPUT->heading($blockname);

$sections = ues_section::from_course($course);

$course_label = function ($course, $section) {
    return "$course->department $course->cou_number $section->sec_number";
};

$to_option = function ($section) use ($course_label) {
    $course = $section->course();
    return $course_label($course, $section);
};

$section_selector = array(0 => $_s('allsections')) + array_map($to_option, $sections);

$baseurl = new moodle_url('view.php', array(
    'id' => $courseid,
    'sectionid' => $sectionid,
    'action' => $action,
    'ln' => $ln,
    'fn' => $fn,
    'order' => $order,
    'dir' => $by
));

$nothing = array('' => $_s('section'));
echo $OUTPUT->single_select($baseurl, 'sectionid', $section_selector, $sectionid, $nothing);

$nothing = array('' => $_s('action'));
$defaults = array(0 => $_s('both'));
$actions = $defaults + array(ues_log::ADD => $_s('add'), ues_log::DROP => $_s('drop'));

echo $OUTPUT->single_select($baseurl, 'action', $actions, $action, $nothing);

echo initial_bar($baseurl->params(), $ln);

$to_tables = function ($in, $section) use ($_s, $course_label, $OUTPUT, $baseurl) {
    $url_params = $baseurl->params();

    $by_params = array('l.sectionid' => $section->id);

    $action_filter = $url_params['action'];
    if ($action_filter) {
        $by_params['l.action'] = $action_filter;
    }

    $firstname_filter = $url_params['fn'];
    if ($firstname_filter) {
        $by_params['u.firstname'] = $firstname_filter;
    }

    $lastname_filter = $url_params['ln'];
    if ($lastname_filter) {
        $by_params['u.lastname'] = $lastname_filter;
    }

    $order = $url_params['order'];
    $by = $url_params['dir'];
    if ($order) {
        $order_by = "$order $by";
    } else {
        $order_by = 'timestamp DESC';
    }

    $logs = ues_log::get_by_special($by_params, $order_by);
    $count = count($logs);

    $n_head = get_string('firstname') . ' / '. get_string('lastname');

    $table = new html_table();
    $table->head = array($n_head, get_string('action'), get_string('time'));
    $table->data = array();

    foreach ($logs as $log) {
        $name = fullname($log);
        $email_link = html_writer::link('mailto:' . $log->email, $name);

        $class = $log->action == 'AD' ? 'add' : 'drop';
        $action = '<span class = "table_'.$class.'">' . $log->action . '</span>';

        $line = array($email_link, $action, strftime('%F %T', $log->timestamp));
        $table->data[] = new html_table_row($line);
    }

    echo $OUTPUT->heading($course_label($section->course(), $section));

    echo '<div class = "tracking_table">' .
            html_writer::table($table) .
         '</div>';

    return $in or !empty($count);
};

if ($sectionid) {
    $sections = ues_section::get_all(array('id' => $sectionid));
}

$success = array_reduce($sections, $to_tables, false);

if (!$success) {
    $OUTPUT->notification($_s('no_logs'));
}

echo initial_bar($baseurl->params(), $ln);

echo $OUTPUT->footer();

function initial_bar($params, $chosen) {
    $strall = get_string('all');

    $html = '<div class="initialbar lastinitial">'. get_string('lastname'). ' : ';

    $make_link = function ($value, $letter=null) use ($params) {
        if (is_null($letter)) {
            $letter = $value;
        }

        $params['ln'] = $letter;

        return html_writer::link(new moodle_url('view.php', $params), $value);
    };

    $bold = function ($text) {
        return '<strong>' . $text . '</strong>';
    };

    if (!empty($chosen)) {
        $html .= $make_link($strall, '');
    } else {
        $html .= $bold($strall);
    }

    $alpha = explode(',', get_string('alphabet', 'langconfig'));
    foreach ($alpha as $letter) {
        if ($letter == $chosen) {
            $html .= ' ' . $bold($letter);
        } else {
            $html .= ' ' . $make_link($letter);
        }
    }

    $html .= '</div>';

    return $html;
}
