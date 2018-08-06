<?php

require_once($config->app_class_dir . '/view.class.php');

class View_Riina extends View {
	
	protected $container_engine;
	
	public function __construct($template = null)
	{
		global $config;
		
		parent::__construct($template);
		
		$this->container_engine = new View('layout.tpl.html');
		
		$this->assign('root', $config->root_url);
		$this->assign('path', $_SERVER['REQUEST_URI']);
	} // function __construct()
	
	public function assign($name, $param)
	{
		parent::assign($name, $param);
		$this->container_engine->assign($name, $param);
	} // function assign()
	
	public function assignByRef($name, $param)
	{
		parent::assignByRef($name, $param);
		$this->container_engine->assignByRef($name, $param);
	} // function assignByRef()
	
	public function render()
	{
		$content = $this->fetch();
		
		$this->container_engine->assignByRef('content', $content);
		$this->container_engine->render();
	} // function render()
	
} // class ViewRiina
