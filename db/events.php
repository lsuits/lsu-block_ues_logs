<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.


/**
 *
 * @package    block_ues_logs
 * @copyright  2014 Louisiana State University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
$events = array(
    'ues_student_release',
    'ues_student_process',
    'ues_section_drop'
);

$to_handler = function ($event) {
    return array(
        'handlerfile' => '/blocks/ues_logs/eventslib.php',
        'handlerfunction' => array('ues_logs_event_handler', $event),
        'schedule' => 'instant'
    );
};

$handlers = array_combine($events, array_map($to_handler, $events));
