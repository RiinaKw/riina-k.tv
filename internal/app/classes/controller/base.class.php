<?php

class Controller_Base extends Controller {
	
	protected $_config = array();
	
	protected function __before()
	{
		global $bootstrap;
		
		$path = $bootstrap->app_config_path('conf/riina-k.tv.conf');
		$this->_config = parse_ini_file($path);
	} // function __before()
	
	protected function _get_config($key)
	{
		return $this->_config[$key];
	} // function _get_config()
	
	protected function url_create($path)
	{
		global $bootstrap;
		
		$first = substr($path, 0, 1);
		return $bootstrap->root_url . ($first == '/' ? $first : '/') . $path;
	} // function url_create()
	
} // class Controller_Base
