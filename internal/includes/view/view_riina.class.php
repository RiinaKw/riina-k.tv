<?php

require_once(INCLUDE_DIR . '/view/view.class.php');

class View_Riina extends View {
	
	protected $container_engine;
	
	public function __construct($template = null)
	{
		parent::__construct($template);
		
		$this->container_engine = new View('layout.tpl.html');
		
		$this->assign('root', ROOT_URL);
		$this->assign('path', $_SERVER['REQUEST_URI']);
	}
	
	public function assign($name, $param)
	{
		parent::assign($name, $param);
		$this->container_engine->assign($name, $param);
	}
	
	public function assignByRef($name, $param)
	{
		parent::assignByRef($name, $param);
		$this->container_engine->assignByRef($name, $param);
	}
	
	public function render()
	{
		$content = $this->fetch();
		
		$this->container_engine->assignByRef('content', $content);
		$this->container_engine->render();
	}
	
} // class ViewRiina
