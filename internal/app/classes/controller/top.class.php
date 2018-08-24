<?php

class Controller_Top extends Controller {
	
	public function action_index()
	{
		global $bootstrap;
		
		$model = new Model_Whatsnew($bootstrap->db);
		$whatsnew = $model->get_all();

		$view = new View_Container('index.tpl.html');
		$view->title = 'rk. tv : あーけーどてぃーゔぃー';
		$view->page_id = 'page-top';
		$view->whatsnew = $whatsnew;
		$view->render();
	}
	
	public function action_about()
	{
		global $bootstrap;
		
		$view = new View_Container('about.tpl.html');
		$view->title = 'about - rk. tv';
		$view->page_id = 'page-about';
		$view->easter_egg = '/artwork/riina';
		$view->render();
	}
	
}