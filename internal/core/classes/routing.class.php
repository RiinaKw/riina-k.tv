<?php

class Routing
{

	public static function route()
	{
		global $bootstrap;

		$routing_definition_path = $bootstrap->app_config_path('routes.inc.php');
		if ( $routing_definition_path && is_file($routing_definition_path) ) {
			$routing = require($routing_definition_path);
		} else {
			$routing = null;
		}

		if ( !isset($_SERVER['PATH_INFO']) ) {
			$controller = 'top';
			$action = 'index';
			$param = array();
		} else {
			$path = substr($_SERVER['PATH_INFO'], 1);
			if ( preg_match('/^(.*)\/$/', $path, $matches) ) {
				$path = $matches[1];
			}

			if ($routing) {
				foreach ($routing as $key => $value) {
					$re = '/^' . str_replace('/', '\/', $key) . '/';
					if ( preg_match($re, $path) ) {
						$path = preg_replace($re, $value, $path);
						break;
					}
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
			trigger_error(
				'class "' . $class . '" has not method "' . $method . '"',
				E_USER_ERROR
			);
		}
	} // function route()

} // class Routing
