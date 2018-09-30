<?php

class Controller_Music extends Controller_Base {
	
	protected function _set_url(&$track)
	{
		$track['detail_url'] = $this->url_create( 'music/' . $track['slug'] );
		if ( $track['preview_url'] ) {
			$track['preview_url'] = $this->url_create( $track['preview_url'] );
		}
		if ( $track['image_name'] != '' ) {
			$track['image_url'] = $this->url_create( 'artwork/' . $track['image_name'] );
		} else {
			$track['image_url'] = $this->url_create( 'images/no-jacket.jpg' );
		}
	}
	
	public function action_index()
	{
		parent::__before();
		
		global $bootstrap;
		
		$model = new Model_Track($bootstrap->db);
		$categories = $model->get_as_category();
		
		foreach ($categories as &$category) {
			foreach ($category['tracks'] as &$track) {
				$this->_set_url($track);
			}
		}

		$view = new View_Container('music.tpl.html');
		$view->page_id = 'page-music';
		$view->categories = $categories;
		
		$view->title = 'music - ' . $this->_get_config('title_en');
		$view->render();
	} // function action_index()
	
	public function action_detail($arg)
	{
		parent::__before();
		
		global $bootstrap;
		
		$slug = $arg[0];
		
		$model = new Model_Track($bootstrap->db);
		$categories = $model->get_as_category();
		
		foreach ($categories as &$category) {
			foreach ($category['tracks'] as &$track) {
				$this->_set_url($track);
			}
		}
		
		$view = new View_Container('music.tpl.html');
		$view->page_id = 'page-music';
		$view->categories = $categories;
		
		$cur_track = $model->get_by_slug($slug);
		$this->_set_url($cur_track);
		
		$view->track = $cur_track;
		$view->title = $cur_track['title'] . ' - ' . $this->_get_config('title_en');
		$view->render();
	} // function action_detail()
	
} // class Controller_Music
