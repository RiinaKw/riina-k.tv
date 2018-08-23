<?php

class Config {
	
	protected $_prop = array();
	
	public function __construct($public_dir)
	{
		$this->_prop['public_dir'] = realpath($public_dir);
		$this->_prop['internal_dir'] = __DIR__;
		
		$this->_prop['app_dir'] = $this->internal_dir('/app');
		$this->_prop['app_class_dir'] = $this->internal_dir('/app/classes');
		
		$this->_prop['core_dir'] = $this->internal_dir('/core');
		$this->_prop['core_class_dir'] = $this->internal_dir('/core/classes');
		
		$this->_prop['view_dir'] = $this->internal_dir('/app/view');
		
		$this->_prop['vendor_dir'] = $this->internal_dir('/vendor');
		
		$this->_prop['server_name'] = $_SERVER['SERVER_NAME'];
		$this->_prop['root_url'] = (empty($_SERVER['HTTPS']) ? 'http://' : 'https://') . $_SERVER['HTTP_HOST'];
		
		$env = realpath($this->_prop['app_dir'] . '/env/' . $this->_prop['server_name'] . '.inc.php');
		if ( !$env || !is_file($env) ) {
			trigger_error('environment error: unknown environment ' . $this->_prop['server_name'], E_USER_ERROR);
		}
		$config = require_once($env);
		$this->_prop['db'] = $config['db'];
		$this->_prop['env'] = $config['env'];
		
		foreach ($config['dir'] as $name => $dir) {
			$this->_prop[$name] = $this->internal_dir($dir);
		}
		
		require_once($this->_prop['vendor_dir'] . '/autoload.php');
		
		spl_autoload_register( array($this, '_class_autoload') );
		set_error_handler( array($this, '_error_handle') );
		set_exception_handler( array($this, '_exception_handle') );
	} // function __construct()
	
	protected function _class_autoload($class_name)
	{
		if ( class_exists($class_name) ) {
			return;
		}
		
		$core_registered_classes = array(
			'HttpException' => 'exception.inc.php',
			'HttpBadRequestException'          => 'exception.inc.php',
			'HttpForbiddenException'           => 'exception.inc.php',
			'HttpNotFoundException'            => 'exception.inc.php',
			'HttpImTeapotException'            => 'exception.inc.php',
			'HttpInternalServerErrorException' => 'exception.inc.php',
			'HttpNotImplementedException'      => 'exception.inc.php',
		);
		
		if ( array_key_exists($class_name, $core_registered_classes) ) {
			$path = $this->_prop['core_class_dir'] . '/' . $core_registered_classes[$class_name];
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
		
		$path = $this->_prop['core_class_dir'] . '/' . $file . '.class.php';
		if ( is_file($path) ) {
			require_once($path);
			if ( !class_exists($class_name) ) {
				trigger_error('class ' . $class_name . ' not found', E_USER_ERROR);
			}
		}
		
		$path = $this->_prop['app_class_dir'] . '/' . $file . '.class.php';
		if ( is_file($path) ) {
			//trigger_error('file ' . $file . ' not found', E_USER_ERROR);
			require_once($path);
			if ( !class_exists($class_name) ) {
				trigger_error('class ' . $class_name . ' not found', E_USER_ERROR);
			}
		} else {
			return false;
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

	protected function internal_dir($dir)
	{
		return realpath(
			$this->_prop['internal_dir'] . '/' . $dir
		);
	} // functiob internal_dir
	
}
