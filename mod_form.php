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
 * @package    mod_videofront
 * @copyright  2018 Eduardo Kraus  {@link http://videofront.com.br}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/course/moodleform_mod.php');

/**
 * @package    mod_videofront
 * @copyright  2018 Eduardo Kraus  {@link http://videofront.com.br}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_videofront_mod_form extends moodleform_mod {

    public function definition() {
        global $CFG, $PAGE;

        $mform = $this->_form;
        $PAGE->requires->jquery();
        $PAGE->requires->js('/mod/videofront/assets/mod_form.js');
        $PAGE->requires->css('/mod/videofront/assets/mod_form.css');

        $mform->addElement('header', 'general', get_string('general', 'form'));

        $mform->addElement('text', 'name', get_string('name_title', 'videofront'), array('size' => '64'));
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');

        $mform->addElement('text', 'identifier', get_string('identifier', 'videofront'), array('size' => '64'));
        $mform->setType('identifier', PARAM_TEXT);
        $mform->addHelpButton('identifier', 'identifier', 'videofront');
        $mform->addRule('identifier', null, 'required', null, 'client');
        $mform->addRule('identifier', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');

        if ($CFG->branch >= 29) {
            $this->standard_intro_elements();
        } else {
            $this->add_intro_editor();
        }

        $this->standard_coursemodule_elements();
        $this->add_action_buttons();
    }
}