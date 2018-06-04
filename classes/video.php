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
        global $USER;

        $post = array(
            "identifier" => $identifier,
            "safetyplayer" => $safetyplayer,
            'cmid' => $cmid,
            'matriculaid' => $USER->id,
            'nome' => fullname($USER),
            'email' => $USER->email
        );

        $baseurl = "api/videos/getplayer/";
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