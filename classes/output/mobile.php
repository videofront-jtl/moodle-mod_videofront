<?php

namespace mod_videofront\output;
defined ( 'MOODLE_INTERNAL' ) || die();

class mobile
{

    public static function mobile_course_view ( $args )
    {
        global $OUTPUT, $USER, $_COOKIE;

        $data = [
            'cmid'    => $args[ 'cmid' ],
            'session' => optional_param ( 'wstoken', '', PARAM_TEXT ),
            'user_id' => $USER->id
        ];

        return array(
            'templates'  => [
                [
                    'id'   => 'main',
                    'html' => $OUTPUT->render_from_template ( 'mod_videofront/mobile_view_page', $data ),
                ],
            ],
            'javascript' => file_get_contents ( __DIR__ . '/mobile.js' ),
            //'javascript' => '(function () { }) ();',
            'otherdata'  => '',
            'files'      => [],
        );
    }
}
