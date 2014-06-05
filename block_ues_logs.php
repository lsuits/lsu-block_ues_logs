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
 * @package    block_ues_logs
 * @copyright  2014 Louisiana State University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_ues_logs extends block_list {
    function init() {
        $this->title = get_string('pluginname', 'block_ues_logs');
    }

    function applicable_formats() {
        return array('site' => false, 'course' => true, 'my' => false);
    }

    function get_content() {
        if ($this->content !== null) {
            return $this->content;
        }

        global $CFG, $COURSE, $OUTPUT;

        $context = context_course::instance($COURSE->id);
        if (!has_capability('moodle/grade:edit', $context)) {
            return $this->content;
        }

        require_once $CFG->dirroot . '/blocks/ues_logs/classes/lib.php';

        $sections = ues_section::from_course($COURSE);

        $by_params = function ($sections) {
            return array('sectionid' => current($sections)->id);
        };

        // No sections or ones with enrollment info
        if (empty($sections) or !ues_log::count($by_params($sections))) {
            return $this->content;
        }

        $course_params = array('id' => $COURSE->id);
        $url = new moodle_url('/blocks/ues_logs/view.php', $course_params);
        $link = html_writer::link($url, $this->title);

        $content = new stdClass;
        $content->items = array($link);
        $content->icons = array($OUTPUT->pix_icon('i/users', $this->title));

        $this->content = $content;

        return $this->content;
    }
}
