<?php

ini_set( 'display_errors', 1 );
error_reporting(E_ALL);

return array(
	'db' => array(
		'dsn'      => 'mysql:host=localhost;dbname=db_example;charset=utf8;',
		'username' => 'user',
		'password' => 'pass',
	),
	'env' => 'development',
	'dir' => array(
		'artwork_dir' => '/artwork',
		'music_dir' => '/music',
		'log_dir' => '/log',
	),
);
