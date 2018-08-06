<?php

class Config {
	
	protected $_prop = array();
	
	public function __construct($public_dir)
	{
		$this->_prop['public_dir'] = realpath($public_dir);
		$this->_prop['internal_dir'] = __DIR__;
		
		$this->_prop['app_class_dir'] = $this->internal_dir('/includes');
		$this->_prop['vendor_dir'] = $this->internal_dir('/vendor');
		
		$this->_prop['server_name'] = $_SERVER['SERVER_NAME'];
		$this->_prop['root_url'] = (empty($_SERVER['HTTPS']) ? 'http://' : 'https://') . $_SERVER['HTTP_HOST'];
		
		$env = realpath($this->_prop['internal_dir'] . '/env/' . $this->_prop['server_name'] . '.inc.php');
		if ( !$env || !is_file($env) ) {
			trigger_error('environment error: unknown environment ' . $this->_prop['server_name'], E_USER_ERROR);
		}
		$config = require_once($env);
		$this->_prop['db'] = $config['db'];
		$this->_prop['env'] = $config['env'];
		
		foreach ($config['dir'] as $name => $dir) {
			$this->_prop[$name] = realpath( $this->_prop['internal_dir'] . $dir );
		}
	}
	
	public function __get($name)
	{
		if ( array_key_exists($name, $this->_prop) ) {
			return $this->_prop[$name];
		} else {
			return null;
		}
	}

	protected function internal_dir($dir)
	{
		return realpath(
			$this->_prop['internal_dir'] . '/' . $dir
		);
	} // functiob internal_dir
	
}
