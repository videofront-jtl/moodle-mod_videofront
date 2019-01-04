<?php
$addons = [
    "mod_videofront" => [
        'handlers' => [
            'coursevideofront' => [
                'delegate' => 'CoreCourseModuleDelegate',
                'method' => 'mobile_course_view',
                'displaydata' => [
                    'icon' => $CFG->wwwroot  . '/mod/videofront/pix/icon.png',
                    'class' => '',
                ],
            ]
        ]
    ]
];
