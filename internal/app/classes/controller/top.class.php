<?php

class Controller_Top extends Controller_Base {
	
	public function action_index()
	{
		parent::__before();
		
		global $bootstrap;
		
		$model = new Model_Whatsnew($bootstrap->db);
		$whatsnew = $model->get_all();

		$view = new View_Container('index.tpl.html');
		$view->title = $this->_config['title'];
		$view->page_id = 'page-top';
		$view->whatsnew = $whatsnew;
		$view->render();
	}
	
	public function action_about()
	{
		parent::__before();
		
		global $bootstrap;
		
		$view = new View_Container('about.tpl.html');
		$view->title = 'about - ' . $this->_config['title_en'];
		$view->page_id = 'page-about';
		$view->easter_egg = '/artwork/riina';
		$view->render();
	}
	
}
