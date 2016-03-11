<?php

defined('MOODLE_INTERNAL') || die();

require_once $CFG->dirroot . '/blocks/ues_logs/classes/lib.php';

class block_ues_logs_observer {

    /**
     * UES event: Log this UES student's drop from this course.
     *
     * @param  \enrol_ues\event\ues_student_released  $event
     * @param  int  other['ues_user_id']
     */
    public static function ues_student_released(\enrol_ues\event\ues_student_released $event) {

        try {
            $ues_student = ues_student::by_id($event->other['ues_user_id']);

            $log = ues_log::drop($ues_student);

            $log->save();

            return true;

        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * UES event: Log this UES student's addition to this course.
     *
     * @param  \enrol_ues\event\ues_student_processed  $event
     * @param  int  other['ues_user_id']
     */
    public static function ues_student_processed(\enrol_ues\event\ues_student_processed $event) {

        try {
            $ues_student = ues_student::by_id($event->other['ues_user_id']);
            
            $log = ues_log::add($ues_student);

            $log->save();

            return true;

        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * UES event: Log this UES section's drop.
     *
     * @param  \enrol_ues\event\ues_section_dropped  $event
     * @param  int  other['ues_section_id']
     */
    public static function ues_section_dropped(\enrol_ues\event\ues_section_dropped $event) {

        try {
            ues_log::delete_all(array('sectionid' => $event->other['ues_section_id']));

            return true;

        } catch (Exception $e) {
            return false;
        }
    }

}