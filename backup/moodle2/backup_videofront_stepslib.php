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
 * Backup videofront activity structure step.
 *
 * @package   mod_videofront
 * @category  backup
 * @copyright 2018 Eduardo Kraus  {@link http://videofront.com.br}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * Backup videofront activity structure step.
 *
 * @package   mod_videofront
 * @category  backup
 * @copyright 2018 Eduardo Kraus  {@link http://videofront.com.br}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backup_videofront_activity_structure_step extends backup_activity_structure_step {

    /**
     * Defines the backup structure of the module
     *
     * @return backup_nested_element
     */
    protected function define_structure() {

        $videofront = new backup_nested_element('videofront', array('id'),
            array('id','course','name','identifier','intro','introformat'));
        $videofront->set_source_table('videofront', array('id' => backup::VAR_ACTIVITYID));
        $videofront->annotate_files('mod_videofront', 'intro', null);
        return $this->prepare_activity_structure($videofront);
    }
}