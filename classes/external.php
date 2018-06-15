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
 * Class for ajax call
 *
 * @package    mod_videofront
 * @copyright  2018 Eduardo Kraus  {@link http://videofront.com.br}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Class for ajax call.
 *
 * @copyright  2018 Eduardo Kraus  {@link http://videofront.com.br}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_videofront_external extends external_api {

    /**
     * Listing parameters.
     *
     * @return external_function_parameters
     */
    public static function listing_parameters() {
        return new \external_function_parameters(
            array(
                'page' => new \external_value(PARAM_INT, 'Instance page of guest enrolment plugin.', VALUE_OPTIONAL),
                'pasta' => new \external_value(PARAM_INT, 'Instance pasta of guest enrolment plugin.', VALUE_OPTIONAL),
                'titulo' => new \external_value(PARAM_TEXT, 'Instance titulo of guest enrolment plugin.', VALUE_OPTIONAL)
            ));
    }

    /**
     * Listing videos.
     *
     * @param $page
     * @param $pasta
     * @param $titulo
     * @return array
     * @throws dml_exception
     * @throws invalid_parameter_exception
     */
    public static function listing($page, $pasta, $titulo) {

        $params = self::validate_parameters(self::listing_parameters(), [
            'page' => $page,
            'pasta' => $pasta,
            'titulo' => $titulo
        ]);

        require(__DIR__ . '/video.php');
        return video::listing($params['page'], $params['pasta'], $params['titulo']);
    }

    /**
     * Listing returns.
     *
     * @return external_single_structure
     */
    public static function listing_returns() {
        return new external_single_structure(
            array(
                'status' => new external_value(PARAM_TEXT, '...'),
                'thumburl' => new external_value(PARAM_TEXT, '...'),
                'thumbreplace' => new external_value(PARAM_TEXT, '...'),
                'page' => new external_value(PARAM_INT, '...'),
                'perpage' => new external_value(PARAM_INT, '...'),
                'numvideos' => new external_value(PARAM_INT, '...'),
                'pasta' => new external_single_structure([
                    'PASTA_ID' => new external_value(PARAM_INT, '...'),
                    'PASTA_TITULO' => new external_value(PARAM_TEXT, '...'),
                    'PASTA_PAI' => new external_value(PARAM_INT, '...'),
                ]),
                'videos' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'ITEM_ID' => new external_value(PARAM_TEXT, '...'),
                            'VIDEO_IDENTIFIER' => new external_value(PARAM_TEXT, '...'),
                            'VIDEO_TITULO' => new external_value(PARAM_TEXT, '...'),
                            'VIDEO_TIPO' => new external_value(PARAM_TEXT, '...'),
                            'VIDEO_FILENAME' => new external_value(PARAM_TEXT, '...'),
                            'VIDEO_STATUS' => new external_value(PARAM_TEXT, '...')
                        )
                    )
                )
            )
        );
    }
}