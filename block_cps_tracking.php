<?php

class block_cps_tracking extends block_list {
    function init() {
        $this->title = get_string('pluginname', 'block_cps_tracking');
    }

    function applicable_formats() {
        return array('site' => false, 'course' => true, 'my' => false);
    }

    function content() {
        if ($this->content !== null) {
            return $this->content;
        }

        $content - new stdClass;
        $content->items = array();
        $content->icons = array();

        $this->content = $content;

        return $this->content;
    }
}
