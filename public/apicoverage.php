<?php

//xdebug_start_code_coverage(XDEBUG_CC_UNUSED | XDEBUG_CC_DEAD_CODE); // PHP7 Segfault
xdebug_start_code_coverage(XDEBUG_CC_DEAD_CODE);

require 'api.php';

$data = xdebug_get_code_coverage();
xdebug_stop_code_coverage();

$dir = dirname(__DIR__) . '/apitest/_coverage/';
$fullPath = $dir . md5(uniqid(rand(), TRUE)) . '.coverage';
file_put_contents($fullPath, serialize($data));
