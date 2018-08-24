<?php

class Bootstrap {
	
	protected $_prop = array();
	
	public function __construct($public_dir)
	{
		$this->_prop['public_dir'] = realpath($public_dir);
		$this->_prop['internal_dir'] = __DIR__;
		
		$this->_prop['app_dir']          = $this->internal_path('/app');
		$this->_prop['app_classes_dir']  = $this->internal_path('/app/classes');
		$this->_prop['app_config_dir']   = $this->internal_path('/app/config');
		$this->_prop['env_dir']          = $this->internal_path('/app/env');
		
		$this->_prop['core_dir']         = $this->internal_path('/core');
		$this->_prop['core_classes_dir'] = $this->internal_path('/core/classes');
		$this->_prop['core_config_dir']  = $this->internal_path('/core/config');
		
		$this->_prop['view_dir']         = $this->internal_path('/app/view');
		
		$this->_prop['vendor_dir']       = $this->internal_path('/vendor');
		
		$this->_prop['root_url'] = (empty($_SERVER['HTTPS']) ? 'http://' : 'https://') . $_SERVER['SERVER_NAME'];
		
		$env_path = $this->env_path( $_SERVER['SERVER_NAME'] );
		if ( !$env_path || !is_file($env_path) ) {
			trigger_error('environment error: unknown environment ' . $_SERVER['SERVER_NAME'], E_USER_ERROR);
		}
		$env = require_once($env_path);
		$this->_prop['db'] = $env['db'];
		$this->_prop['env'] = $env['env'];
		
		foreach ($env['dir'] as $name => $dir) {
			$this->_prop[$name] = $this->internal_path($dir);
		}
		
		$vendor_autoload_path = $this->vendor_path('autoload.php');
		if ( !$vendor_autoload_path || !is_file($vendor_autoload_path) ) {
			trigger_error('vendor autoload not found', E_USER_ERROR);
		}
		require_once($vendor_autoload_path);
		
		spl_autoload_register( array($this, '_class_autoload') );
		set_error_handler( array($this, '_error_handle') );
		set_exception_handler( array($this, '_exception_handle') );
	} // function __construct()
	
	protected function _class_autoload($class_name)
	{
		if ( class_exists($class_name) ) {
			return;
		}
		
		$registered_classes_path = $this->core_config_path('registered_classes');
		if ( !$registered_classes_path || !is_file($registered_classes_path) ) {
			trigger_error('file "registered_classes.inc.php" not found', E_USER_ERROR);
		}
		$core_registered_classes = require($registered_classes_path);
		
		if ( array_key_exists($class_name, $core_registered_classes) ) {
			$path = $this->core_path( $core_registered_classes[$class_name] );
			if ( !is_file($path) ) {
				trigger_error('file ' . $path . ' not found', E_USER_ERROR);
			}
			require_once($path);
			if ( !class_exists($class_name) ) {
				trigger_error('class ' . $class_name . ' not found', E_USER_ERROR);
			}
			return;
		}
		
		$lower = strtolower($class_name);
		$arr = explode('_', $lower);
		$file = implode('/', $arr);
		
		$path = $this->core_classes_path($file);
		if ( is_file($path) ) {
			require_once($path);
			if ( !class_exists($class_name) ) {
				trigger_error('class ' . $class_name . ' not found', E_USER_ERROR);
			} else {
				return true;
			}
		}
		
		$path = $this->app_classes_path($file);
		if ( is_file($path) ) {
			require_once($path);
			if ( !class_exists($class_name) ) {
				trigger_error('class ' . $class_name . ' not found', E_USER_ERROR);
			}
		} else {
			trigger_error('file ' . $file . ' not found', E_USER_ERROR);
			//return false;
		}
		return true;
	} // function _class_autoload()
	
	public function _error_handle($errno, $errstr, $errfile, $errline)
	{
		throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
	} // function _error_handle()
	
	public function _exception_handle($e)
	{
		$view = new View('error.tpl.php');
		$view->title = $e->getMessage();
		
		if ($this->env == 'production') {
			$view->message = $e->getMessage();
			$view->trace = '';
		} else {
			$view->message = $e->getMessage() . ' in ' . $e->getFile() . '(' . $e->getLine() . ')';
			$view->trace = $e->getTraceAsString();
		}
		$view->render();
	} // function _exception_handle()
	
	public function __get($name)
	{
		if ( array_key_exists($name, $this->_prop) ) {
			return $this->_prop[$name];
		} else {
			return null;
		}
	} // function __get()

	public function internal_path($file)
	{
		return realpath(
			$this->_prop['internal_dir'] . '/' . $file
		);
	} // function internal_path()
	
	public function core_path($file)
	{
		return realpath(
			$this->_prop['core_dir'] . '/' . $file
		);
	} // function core_path()
	
	public function core_classes_path($file)
	{
		return realpath(
			$this->_prop['core_classes_dir'] . '/' . $file . '.class.php'
		);
	} // function core_classes_path()
	
	public function core_config_path($file)
	{
		return realpath(
			$this->_prop['core_config_dir'] . '/' . $file . '.inc.php'
		);
	} // function core_config_path()
	
	public function app_path($file)
	{
		return realpath(
			$this->_prop['app_dir'] . '/' . $file
		);
	} // function app_path()
	
	public function app_classes_path($file)
	{
		return realpath(
			$this->_prop['app_classes_dir'] . '/' . $file . '.class.php'
		);
	} // function app_classes_path()
	
	public function app_config_path($file)
	{
		return realpath(
			$this->_prop['app_config_dir'] . '/' . $file . '.inc.php'
		);
	} // function app_config_path()
	
	public function env_path($server_name)
	{
		return realpath(
			$this->_prop['env_dir'] . '/' . $server_name . '.inc.php'
		);
	} // function env_path()
	
	public function vendor_path($file)
	{
		return realpath(
			$this->_prop['vendor_dir'] . '/' . $file
		);
	} // function vendor_path()
	
	public function view_path($file)
	{
		return realpath(
			$this->_prop['view_dir'] . '/' . $file
		);
	} // function view_path()
	
	public function route()
	{
		$routing_definition_path = $this->app_config_path('routes');
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
			trigger_error('class "' . $class . '" has not method "' . $method . '"', E_USER_ERROR);
		}
	} // function route()
} // class Bootstrap
