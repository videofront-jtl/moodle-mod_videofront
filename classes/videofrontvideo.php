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
 * Video class.
 *
 * @package    mod_videofront
 * @copyright  2018 Eduardo Kraus  {@link http://videofront.com.br}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

define("VIDEOFRONTVIDEO", true);

/**
 * Class videofrontvideo.
 *
 * @copyright  2018 Eduardo Kraus  {@link http://videofront.com.br}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class videofrontvideo {

    /**
     * Call for list videos in videoteca.
     *
     * @param int $page
     * @param int $pasta
     * @param string $titulo
     *
     * @return array
     */
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

    /**
     * Call for get player code.
     *
     * @param int $cmid
     * @param string $identifier
     * @param string $safetyplayer
     *
     * @return string
     */
    public static function getplayer($cmid, $identifier, $safetyplayer) {
        global $USER;
        $config = get_config('videofront');

        $payload = array(
            "identifier" => $identifier,
            "matricula" => $cmid,
            "nome" => fullname($USER),
            "email" => $USER->email,
            "safetyplayer" => $safetyplayer
        );

        require_once __DIR__ . "/crypt/jwt.php";
        $token = \mod_videofront\crypt\jwt::encode($config->token, $payload);

        return "
            <div id='videoteca-background'>
                <iframe width='100%' height='100%' frameborder='0'  
                        id='videoteca-video' allowfullscreen
                        src='{$config->url}Embed/iframe/?token={$token}'></iframe>
            </div>
            <script>
                window.onload = function() {
                    var videoBoxWidth = 0;

                    var videoBox = document.getElementById('videoteca-background');
                    if (videoBox.offsetWidth) {
                        videoBoxWidth = videoBox.offsetWidth;
                    } else if (videoBox.clientWidth) {
                        videoBoxWidth = videoBox.clientWidth;
                    }

                    var videohd1 = document.getElementById('videoteca-video');
                    var videoBoxHeight2   = videoBoxWidth * 9 / 16;
                    videohd1.style.width  = videoBoxWidth + 'px';
                    videohd1.style.height = videoBoxHeight2 + 'px';
                };
            </script>";

        //$post = array(
        //    "identifier" => $identifier,
        //    "safetyplayer" => $safetyplayer,
        //    "user_agent" => $_SERVER['HTTP_USER_AGENT'],
        //    'cmid' => $cmid
        //);
        //
        //$baseurl = "api/videos/getplayer/";
        //return self::load($baseurl, $post);
    }

    /**
     * get Kapture link
     *
     * @return string
     */
    public static function getkapturelink() {
        global $USER;

        $post = [
            'email' => $USER->email,
            'nome' => fullname($USER)
        ];

        $baseurl = "api/kapture/getlink/";
        return self::load($baseurl, $post);
    }

    /**
     * Curl execution.
     *
     * @param string $baseurl
     * @param array $post
     *
     * @return string
     */
    private static function load($baseurl, $post) {
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
