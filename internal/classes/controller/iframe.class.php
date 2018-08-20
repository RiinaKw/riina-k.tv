<?php

class Controller_Iframe extends Controller {
	
	public function action_detail($arg)
	{
		global $config;
		
		$preview_url = $config->root_url . '/preview/' . $arg[0];
		$download_url = $preview_url . '/download';

		$view = new View_Smarty('iframe.tpl.html');
		$view->preview_url = $preview_url;
		$view->download_url = $download_url;
		$view->render();
	}
	
}