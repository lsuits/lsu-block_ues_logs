<?php

class block_cps_tracking extends block_list {
    function init() {
        $this->title = get_string('pluginname', 'block_cps_tracking');
    }

    function applicable_formats() {
        return array('site' => false, 'course' => true, 'my' => false);
    }

    function get_content() {
        if ($this->content !== null) {
            return $this->content;
        }

        global $CFG, $COURSE, $OUTPUT;

        $context = get_context_instance(CONTEXT_COURSE, $COURSE->id);
        if (!has_capability('moodle/grade:edit', $context)) {
            return $this->content;
        }

        require_once $CFG->dirroot . '/blocks/cps_tracking/classes/lib.php';

        $sections = cps_section::from_course($COURSE);

        $by_params = function ($sections) {
            return array('sectionid' => current($sections)->id);
        };

        // No sections or ones with enrollment info
        if (empty($sections) or !cps_log::count($by_params($sections))) {
            return $this->content;
        }

        $course_params = array('id' => $COURSE->id);
        $url = new moodle_url('/blocks/cps_tracking/view.php', $course_params);
        $link = html_writer::link($url, $this->title);

        $content = new stdClass;
        $content->items = array($link);
        $content->icons = array($OUTPUT->pix_icon('i/users', $this->title));

        $this->content = $content;

        return $this->content;
    }
}
