<?php

class Controller_About extends Controller {
	
	public function action_index()
	{
		global $config;
		
		$view = new View_Container('about.tpl.html');
		$view->title = 'about - rk. tv';
		$view->page_id = 'page-about';
		$view->easter_egg = '/artwork/riina';
		$view->render();
	}
	
}