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
     * @throws dml_exception
     */
    public static function listing($page, $pasta, $titulo) {
        $post = array(
            "page" => $page,
            "pastaid" => $pasta,
            "titulo" => $titulo
        );

        $baseurl = "api/v2/video";
        $json = self::load($baseurl, $post, "GET");

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
     * @throws dml_exception
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
                <iframe style='position: relative; width: 1920px; max-width: 100%; aspect-ratio: 1920/1080; border: none;'
                        id='videoteca-video' allowfullscreen
                        src='{$config->url}Embed/iframe/?token={$token}'></iframe>
            </div>
            <script>
                window.addEventListener('message', receiveMessage, false);
                function receiveMessage(event)
                {
                    if ( event.data.local !== 'vfplayer' ) {
                        return;
                    }
                    if ( event.data.nomeMensagem !== 'start-player' ) {
                        var videoBoxWidth = 0;
                        var ratio = event.data.ratio.split(':');
                        var videoBox = document.getElementById('videoteca-background');
                        if (videoBox.offsetWidth) {
                            videoBoxWidth = videoBox.offsetWidth;
                        } else if (videoBox.clientWidth) {
                            videoBoxWidth = videoBox.clientWidth;
                        }
    
                        var videohd1 = document.getElementById('videoteca-video');
                        var videoBoxHeight2   = videoBoxWidth * ratio[1] / ratio[0];
                        videohd1.style.width  = videoBoxWidth + 'px';
                        videohd1.style.height = videoBoxHeight2 + 'px';
                    }
                }
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
     * Call for get status.
     *
     * @param string $identifier
     *
     * @return string
     * @throws dml_exception
     */
    public static function getstatus($identifier) {
        $baseurl = "api/v2/video/{$identifier}/status/";
        return json_decode(self::load($baseurl, null, "GET"));
    }

    /**
     * get Kapture link
     *
     * @param string $identifier
     * @return string
     * @throws dml_exception
     */
    public static function getkapturelink($identifier = '') {
        global $USER;

        $post = [
            'email' => $USER->email,
            'nome' => fullname($USER),
            'identifier' => $identifier
        ];

        $baseurl = "api/kapture/getlink/";
        return self::load($baseurl, $post, "POST");
    }

    /**
     * Curl execution.
     *
     * @param string $baseurl
     * @param array $query
     *
     * @param string $protocol
     * @return string
     * @throws dml_exception
     */
    private static function load($baseurl, $query = null, $protocol = "GET") {
        $config = get_config('videofront');

        $ch = curl_init();

        $query = http_build_query($query, '', '&');

        if ($protocol == "POST") {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $query);

            $queryUrl = "";
        } else if ($query) {
            $queryUrl = "?{$query}";
        }

        curl_setopt($ch, CURLOPT_URL, "{$config->url}{$baseurl}{$queryUrl}");

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $protocol);

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
