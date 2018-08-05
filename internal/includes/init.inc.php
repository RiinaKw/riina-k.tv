<?php

define( 'INCLUDE_DIR', realpath( dirname(__FILE__) ) );
define( 'SERVER_NAME', $_SERVER['SERVER_NAME'] );

$env = realpath(INCLUDE_DIR . '/env/' . SERVER_NAME . '.inc.php');
if ( $env && is_file($env) ) {
	return require_once($env);
} else {
	trigger_error('environment error: unknown environment ' . SERVER_NAME, E_USER_ERROR);
	return false;
}
