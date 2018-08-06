<?php

class Controller_About extends Controller {
	
	public function action_index()
	{
		global $config;
		
		$view = new View_Container('about.tpl.html');
		$view->assign('title', 'about - rk. tv');
		$view->assign('page_id', 'page-about');
		$view->assign('easter_egg', '/artwork/riina');
		$view->render();
	}
	
}