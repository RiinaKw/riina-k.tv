<?php

class Controller_Music extends Controller {
	
	public function action_index()
	{
		global $config;
		
		$model = new Model_Track($config->db);
		$categories = $model->get_as_category();

		$view = new View_Container('music.tpl.html');
		$view->page_id = 'page-music';
		$view->categories = $categories;
		
		$view->title = 'music - rk. tv';
		$view->render();
	}
	
	public function action_detail($arg)
	{
		global $config;
		
		$slug = $arg[0];
		
		$model = new Model_Track($config->db);
		$categories = $model->get_as_category();
		
		$view = new View_Container('music.tpl.html');
		$view->page_id = 'page-music';
		$view->categories = $categories;
		
		$track = $model->get_by_slug($slug);
		$view->track = $track;
		$view->title = $track['title'] . ' - rk. tv';
		$view->render();
	}
	
}