<?php

mb_language('ja');
mb_internal_encoding('UTF-8');
mb_regex_encoding('UTF-8');

require_once('../internal/bootstrap.inc.php');
$bootstrap =  new Bootstrap(__DIR__);

if ($bootstrap->env == 'development') {
	ini_set( 'display_errors', 1 );
	error_reporting(E_ALL);
}

Routing::route();
