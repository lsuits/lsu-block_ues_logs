<?php

require_once $CFG->dirroot . '/blocks/cps_tracking/classes/lib.php';

abstract class cps_tracking_event_handler {
    public static function cps_student_process($cps_student) {
        try {
            $log = cps_log::add($cps_student);

            $log->save();

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function cps_student_release($cps_student) {
        try {
            $log = cps_log::drop($cps_student);

            $log->save();

            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
