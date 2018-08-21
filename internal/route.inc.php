<?php

$routing = require($config->app_dir . '/route.inc.php');

if ( !isset($_SERVER['PATH_INFO']) ) {
	$controller = 'top';
	$action = 'index';
	$param = array();
} else {
	$path = substr($_SERVER['PATH_INFO'], 1);
	
	foreach ($routing as $key => $value) {
		$re = '/^' . str_replace('/', '\/', $key) . '/';
		if ( preg_match($re, $path) ) {
			$path = preg_replace($re, $value, $path);
			break;
		}
	}
	
	$arr = explode('/', $path);
	$controller = array_shift($arr);
	
	$action = 'index';
	if ($arr && $arr[0]) {
		$action = array_shift($arr);
	} else if (isset($arr[0])) {
		array_shift($arr);
	}
	
	$param = array();
	if ($arr) {
		$param = $arr;
	}
}

$class = 'Controller_' . ucfirst($controller);
$method = 'action_' . $action;

$instance = new $class;
if ( method_exists($instance, $method) ) {
	$instance->$method($param);
} else {
	trigger_error('class "' . $class . '" has not method "' . $method . '"', E_USER_ERROR);
}
