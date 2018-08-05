<?php

mb_language('ja');
mb_internal_encoding('UTF-8');
mb_regex_encoding('UTF-8');

ini_set( 'display_errors', 1 );
error_reporting(E_ALL);

require_once('../internal/init.inc.php');
return new Config(__DIR__);
