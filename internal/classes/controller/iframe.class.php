<?php

class Controller_Iframe extends Controller {
	
	public function action_detail($arg)
	{
		global $config;
		
		$preview_url = $config->root_url . '/preview/' . $arg[0];
		$download_url = $preview_url . '/download';

		$view = new View('iframe.tpl.html');
		$view->assignByRef('preview_url', $preview_url);
		$view->assignByRef('download_url', $download_url);
		$view->render();
	}
	
}