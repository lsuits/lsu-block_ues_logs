<?php

require_once $CFG->dirroot . '/blocks/cps_tracking/classes/lib.php';

abstract class cps_tracking_event_handler {
    public static function cps_student_process($cps_student) {
        return true;
    }

    public static function cps_student_release($cps_student) {
        return true;
    }
}
