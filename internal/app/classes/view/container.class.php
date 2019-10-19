<?php

class View_Container extends View_Smarty
{
	public function meta($type = 'page', $track = null)
	{
		switch ($type) {
			case 'page':
			default:
				$meta = new View_Smarty('meta/meta_page.tpl.html');
				break;
			case 'track':
				$meta = new View_Smarty('meta/meta_track.tpl.html');
				$meta->track = $track;
				break;
		}
		$this->_engine->assign('meta', $meta->fetch());
	} // function meta()

} // class View_Container
