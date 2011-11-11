<?php

require_once $CFG->dirroot . '/enrol/cps/publiclib.php';
cps::require_daos();

interface cps_log_types {
    const ADD = 'AD';
    const DROP = 'DR';
}

class cps_log extends cps_external implements cps_log_types {
    var $userid;
    var $sectionid;
    var $action;
    var $timestamp;

    public static function add($cps_user) {
        return self::make(self::ADD, $cps_user);
    }

    public static function drop($cps_user) {
        return self::make(self::DROP, $cps_user);
    }

    public static function get_by_special($params, $order = 'timestamp DESC') {
        global $DB;

        $to_flatten = function($key, $value) {
            $safe_value = addslashes($value);

            if (preg_match('/name$/', $key)) {
                $filter = " LIKE '$safe_value%'";
            } else {
                $filter = " = '$safe_value'";
            }

            return "$key $filter";
        };

        $f = array_map($to_flatten, array_keys($params), array_values($params));

        $where = (!empty($f) ? ' AND ' : '') . implode(' AND ', $f);

        $sql = "SELECT l.userid, u.firstname, u.lastname, l.action, l.timestamp
            FROM {enrol_cps_logs} l,
                 {user} u
                 WHERE u.id = l.userid $where ORDER BY $order";

        $upgraded = function($log) { return cps_log::upgrade($log); };

        return array_map($upgraded, $DB->get_records_sql($sql));
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
