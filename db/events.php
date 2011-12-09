<?php

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
