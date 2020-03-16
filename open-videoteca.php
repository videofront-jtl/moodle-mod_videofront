<?php
/**
 * User: Eduardo Kraus
 * Date: 02/09/2019
 * Time: 21:54
 */

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');

$config = get_config('videofront');

redirect("{$config->url}Video/addVideo?pai=0");
