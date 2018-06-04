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

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    require_once($CFG->libdir . "/resourcelib.php");

    $settings->add(new admin_setting_configtext('videofront/url',
        get_string('url_title', 'videofront'),
        get_string('url_desc', 'videofront'), ''));

    $settings->add(new admin_setting_configtext('videofront/token',
        get_string('token_title', 'videofront'),
        get_string('token_desc', 'videofront'), ''));

    $itensseguranca = array(
        'none' => 'Nada',
        'id' => 'ID do Aluno',
    );

    $infofields = $DB->get_records('user_info_field');
    foreach ($infofields as $infofield) {
        $itensseguranca["profile_{$infofield->id}"] = $infofield->name;
    }

    $settings->add(new admin_setting_configselect('videofront/safety',
        get_string('safety_title', 'videofront'),
        get_string('safety_desc',  'videofront'), 'id',
        $itensseguranca
    ));
}
