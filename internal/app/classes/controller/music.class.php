<?php

class Controller_Music extends Controller_Base {
	
	public function action_index()
	{
		parent::__before();
		
		global $bootstrap;
		
		$model = new Model_Track($bootstrap->db);
		$categories = $model->get_as_category();

		$view = new View_Container('music.tpl.html');
		$view->page_id = 'page-music';
		$view->categories = $categories;
		
		$view->title = 'music - ' . $this->_config['title_en'];
		$view->render();
	}
	
	public function action_detail($arg)
	{
		parent::__before();
		
		global $bootstrap;
		
		$slug = $arg[0];
		
		$model = new Model_Track($bootstrap->db);
		$categories = $model->get_as_category();
		
		$view = new View_Container('music.tpl.html');
		$view->page_id = 'page-music';
		$view->categories = $categories;
		
		$track = $model->get_by_slug($slug);
		$view->track = $track;
		$view->title = $track['title'] . ' - ' . $this->_config['title_en'];
		$view->render();
	}
	
}