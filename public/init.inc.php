<?php

mb_language('ja');
mb_internal_encoding('UTF-8');
mb_regex_encoding('UTF-8');

ini_set( 'display_errors', 1 );
error_reporting(E_ALL);

define( 'PUBLIC_DIR', realpath( dirname(__FILE__) ) );
define( 'ROOT_URL', (empty($_SERVER['HTTPS']) ? 'http://' : 'https://') . $_SERVER['HTTP_HOST'] );

return require_once('../internal/includes/init.inc.php');
