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
	}
	
} // class Controller_Base
