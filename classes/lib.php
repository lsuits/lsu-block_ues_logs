<?php

require_once $CFG->dirroot . '/enrol/ues/publiclib.php';
ues::require_daos();

interface ues_log_types {
    const ADD = 'AD';
    const DROP = 'DR';
}

class ues_log extends ues_external implements ues_log_types {
    var $userid;
    var $sectionid;
    var $action;
    var $timestamp;

    public static function add($ues_user) {
        return self::make(self::ADD, $ues_user);
    }

    public static function drop($ues_user) {
        return self::make(self::DROP, $ues_user);
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
            FROM {enrol_ues_logs} l,
                 {user} u
                 WHERE u.id = l.userid $where ORDER BY $order";

        $upgraded = function($log) { return ues_log::upgrade($log); };

        return array_map($upgraded, $DB->get_records_sql($sql));
    }

    private static function make($type, $ues_user) {
        $log = new ues_log();
        $log->action = $type;
        $log->timestamp = time();
        $log->userid = $ues_user->userid;
        $log->sectionid = $ues_user->sectionid;

        return $log;
    }
}
