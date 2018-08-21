<?php

class View_Container extends View_Smarty {
	
	protected $_container_engine;
	
	public function __construct($template = null)
	{
		global $config;
		
		parent::__construct($template);
		
		$this->_container_engine = new View_Smarty('layout.tpl.html');
		
		$this->root = $config->root_url;
		$this->path = $_SERVER['REQUEST_URI'];
	} // function __construct()
	
	public function render()
	{
		$this->_container_engine->assign($this->_prop);
		$this->_container_engine->content = $this->fetch();
		$this->_container_engine->render();
	} // function render()
	
} // class ViewRiina
