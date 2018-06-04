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

class video {

    public static function listing($page, $pasta, $titulo) {
        $post = array(
            "page" => $page,
            "pasta" => $pasta,
            "titulo" => $titulo
        );

        $baseurl = "api/videos/list/";
        $json = self::load($baseurl, $post);

        return json_decode($json);
    }

    public static function getplayer($cmid, $identifier, $safetyplayer) {
        global $CFG, $USER;

        $querydata = array(
            'cmid' => $cmid,
            'identifier' => $identifier,
            'matriculaid' => $USER->id,
            'nome' => fullname($USER),
            "safetyplayer" => $safetyplayer,
            'email' => $USER->email,
        );

        $post = array(
            "identifier" => $identifier,
            "safetyplayer" => $safetyplayer,
            "baseurl" => "{$CFG->wwwroot}/mod/videofront/video_api.php?" . http_build_query($querydata)
        );

        $baseurl = "api/videos/getplayer/";
        return self::load($baseurl, $post);
    }

    public static function getvideo_hls() {
        global $CFG;

        $querydata = array(
            'cmid' => optional_param('cmid', 0, PARAM_TEXT),
            'identifier' => optional_param('identifier', 0, PARAM_TEXT),
            'matriculaid' => optional_param('matriculaid', 0, PARAM_TEXT),
            'nome' => optional_param('nome', 0, PARAM_TEXT),
            'email' => optional_param('email', 0, PARAM_TEXT)
        );

        $post = array(
            "identifier" => optional_param('identifier', 0, PARAM_TEXT),
            "safetyplayer" => optional_param('safetyplayer', 0, PARAM_TEXT),
            "filename" => optional_param('filename', 0, PARAM_TEXT),
            "baseurl" => "{$CFG->wwwroot}/mod/videofront/video_api.php?" . http_build_query($querydata)
        );

        $baseurl = "api/videos/getvideo_hls/";
        return self::load($baseurl, $post);
    }

    private static function load($baseurl, $post = null) {

        $config = get_config('videofront');

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $config->url . $baseurl);

        if ($post != null) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        }

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "authorization:{$config->token}"
        ));

        $output = curl_exec($ch);
        curl_close($ch);

        return $output;
    }
}