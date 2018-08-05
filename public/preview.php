<?php

$config = require_once('init.inc.php');

require_once(INCLUDE_DIR . '/exception.inc.php');
require_once(INCLUDE_DIR . '/model/model_track.class.php');
require_once(INCLUDE_DIR . '/model/model_log.class.php');


if ( isset($_SERVER['PATH_INFO']) ) {
	$arr = explode( '/', $_SERVER['PATH_INFO'] );
	$name = ( isset($arr[1]) ? $arr[1] : '' );
	$mode = ( isset($arr[2]) ? $arr[2] : '' );
}
/*
$arrMode = array(
	'preview' => '-V8',
	'download' => ''
);*/
$extension = '.mp3';
$suffix = '-V8';

try {
	if ( !$name ) {
		throw new HttpBadRequestException('track name missing');
	}
	
	$model = new Model_Track($config['db']);
	$track = $model->get_by_slug($name);
	if ( !$track ) {
		throw new HttpNotFoundException('track "' . $name . '" not exists');
	}
	
	$path = MUSIC_DIR . '/' . $name . $suffix . $extension;
	if ( !file_exists($path) ) {
		throw new HttpInternalServerErrorException('missing track "' . $name . '"');
	} else {
		$size = filesize($path);
		// output to log
		$log = new Model_Log( LOG_DIR . '/music.log', 'a' );
		$log->append($name, $suffix);
		
		// output file
		header('Content-type: audio/mp3');
		header('Content-Length: ' . $size);
		if ($mode == 'download') {
			header( 'Content-Disposition: attachment; filename="' . $name . $extension . '"' );
		}
		echo file_get_contents($path);
	}
} catch (HttpException $e) {
	
	$e->render();
	exit;
	
}

