<?php

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
