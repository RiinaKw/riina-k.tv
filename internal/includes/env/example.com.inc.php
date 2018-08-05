<?php

define( 'PRIVATE_DIR', realpath(INCLUDE_DIR . '/..') );
define( 'ARTWORK_DIR', realpath(PRIVATE_DIR . '/artwork')  );
define( 'MUSIC_DIR', realpath(PRIVATE_DIR . '/music')  );
define( 'LOG_DIR', realpath(PRIVATE_DIR . '/log')  );

ini_set( 'display_errors', 1 );
error_reporting(E_ALL);

return array(
	'db' => array(
		'dsn'      => 'mysql:host=localhost;dbname=db_name;charset=utf8;',
		'username' => 'user',
		'password' => 'pass',
	),
	'env' => 'development',
);
