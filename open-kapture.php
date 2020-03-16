<?php
/**
 * User: Eduardo Kraus
 * Date: 02/09/2019
 * Time: 21:22
 */

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once(__DIR__ . '/classes/videofrontvideo.php');

$url = videofrontvideo::getkapturelink();

redirect ($url);
