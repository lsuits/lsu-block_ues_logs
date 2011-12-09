<?php

require_once $CFG->dirroot . '/blocks/ues_logs/classes/lib.php';

abstract class ues_logs_event_handler {
    public static function ues_student_process($ues_student) {
        try {
            $log = ues_log::add($ues_student);

            $log->save();

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function ues_student_release($ues_student) {
        try {
            $log = ues_log::drop($ues_student);

            $log->save();

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function ues_section_drop($ues_section) {
        return ues_log::delete_all(array('sectionid' => $ues_section->id));
    }
}
