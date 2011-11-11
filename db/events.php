<?php

$events = array(
    'cps_student_release',
    'cps_student_process',
    'cps_section_drop'
);

$to_handler = function ($event) {
    return array(
        'handlerfile' => '/blocks/cps_tracking/eventslib.php',
        'handlerfunction' => array('cps_tracking_event_handler', $event),
        'schedule' => 'instant'
    );
};

$handlers = array_combine($events, array_map($to_handler, $events));
