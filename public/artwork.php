<?php

ini_set( 'display_errors', 1 );
error_reporting(E_ALL);

$config = require_once('./init.inc.php');
require_once(INCLUDE_DIR . '/image.class.php');

try {

	$filename = '';
	if ( isset($_SERVER['PATH_INFO']) ) {
		$arr = explode( '/', $_SERVER['PATH_INFO'] );
		$filename = ( isset($arr[1]) ? $arr[1] : '' );
		$width = ( isset($arr[2]) ? $arr[2] : '');
		$height = ( isset($arr[3]) ? $arr[3] : '');
	}
	if ( !$filename ) {
		throw new HttpBadRequestException('file name missing');
	}
	
	if ($filename == 'riina') {
		throw new HttpImTeapotException('おれはやかんだ (Easter Egg)');
	}
	
	$path = ARTWORK_DIR . '/' . $filename;
	if ( !is_file($path) ) {
		throw new HttpNotFoundException('file "' . $filename . '" not exists');
	}
	
	$image = new Image($path);
	//$image->resample($width, $height)->sharp()->png();
	$image->thru();
	
} catch (HttpException $e) {
	
	$e->render();
	exit;
	
}
