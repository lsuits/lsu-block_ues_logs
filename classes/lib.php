<?php

require_once $CFG->dirroot . '/enrol/cps/publiclib.php';
cps::require_daos();

interface cps_log_types {
    const ADD = 'AD';
    const DROP = 'DR';
}

class cps_log extends cps_external implements cps_log_types {

    public static function add($cps_user) {
        return self::make(self::ADD, $cps_user);
    }

    public static function drop($cps_user) {
        return self::make(self::DROP, $cps_user);
    }

    private static function make($type, $cps_user) {
        $log = new cps_log();
        $log->action = $type;
        $log->timestamp = time();
        $log->userid = $cps_user->userid;
        $log->sectionid = $cps_user->sectionid;

        return $log;
    }
}
