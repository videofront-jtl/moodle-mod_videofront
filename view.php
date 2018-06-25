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
 * Videofront view.
 *
 * @package    mod_videofront
 * @copyright  2018 Eduardo Kraus  {@link http://videofront.com.br}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once(dirname(__FILE__) . '/lib.php');

$id = optional_param('id', 0, PARAM_INT);
$n = optional_param('n', 0, PARAM_INT);

if ($id) {
    $cm = get_coursemodule_from_id('videofront', $id, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $videofront = $DB->get_record('videofront', array('id' => $cm->instance), '*', MUST_EXIST);
} else if ($n) {
    $videofront = $DB->get_record('videofront', array('id' => $n), '*', MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $videofront->course), '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('videofront', $videofront->id, $course->id, false, MUST_EXIST);
} else {
    print_error('You must specify a course_module ID or an instance ID');
}

require_login($course, true, $cm);

$event = \mod_videofront\event\course_module_viewed::create(array(
    'objectid' => $PAGE->cm->instance,
    'context' => $PAGE->context,
));
$event->add_record_snapshot('course', $PAGE->course);
$event->add_record_snapshot($PAGE->cm->modname, $videofront);
$event->trigger();

$PAGE->set_url('/mod/videofront/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($videofront->name));
$PAGE->set_heading(format_string($course->fullname));

echo $OUTPUT->header();
echo $OUTPUT->heading($videofront->name);
if ($videofront->intro) {
    echo $OUTPUT->box(format_module_intro('videofront', $videofront, $cm->id), 'generalbox mod_introbox', 'videofrontintro');
}

$config = get_config('videofront');

$safetyplayer = "";
if ($config->safety) {
    $safety = $config->safety;
    if (strpos($safety, "profile") === 0) {
        $safety = str_replace("profile_", "", $safety);
        $safetyplayer = $USER->profile->$safety;
    } else {
        $safetyplayer = $USER->$safety;
    }
}

if (!defined('VIDEOFRONTVIDEO')) {
    require_once(__DIR__ . '/classes/videofrontvideo.php');
}
$player = videofrontvideo::getplayer($id, $videofront->identifier, $safetyplayer);

echo $OUTPUT->box($player, 'generalbox player', 'videofrontintro');
echo $OUTPUT->footer();
