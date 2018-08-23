<?php

mb_language('ja');
mb_internal_encoding('UTF-8');
mb_regex_encoding('UTF-8');

require_once('../internal/config.inc.php');
$config =  new Config(__DIR__);

if ($config->env == 'development') {
	ini_set( 'display_errors', 1 );
	error_reporting(E_ALL);
}

$config->route();
