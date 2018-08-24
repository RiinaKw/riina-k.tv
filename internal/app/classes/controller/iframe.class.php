<?php

class Controller_Iframe extends Controller_Base {
	
	public function action_detail($arg)
	{
		parent::__before();
		
		global $bootstrap;
		
		$slug = ( isset($arg[0]) ? $arg[0] : '' );
		if ( !$slug ) {
			throw new HttpBadRequestException('slug missing');
		}
		
		$model = new Model_Track($bootstrap->db);
		$track = $model->get_by_slug($slug);
		if ( !$track ) {
			throw new HttpNotFoundException('track "' . $slug . '" not exists');
		}
		
		$preview_url = $bootstrap->root_url . '/preview/' . $arg[0];
		$download_url = $preview_url . '/download';

		$view = new View_Smarty('iframe.tpl.html');
		$view->title = $track['title'] . ' - ' . $this->_config['title_en'];
		$view->preview_url = $preview_url;
		$view->download_url = $download_url;
		$view->render();
	}
	
}
