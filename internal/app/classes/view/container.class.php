<?php

class View_Container extends View_Smarty
{

	protected $_container_engine;

	public function __construct($template = null)
	{
		parent::__construct($template);

		$this->_container_engine = new View_Smarty('layout.tpl.html');
	} // function __construct()

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
		$this->_container_engine->meta = $meta->fetch();
	} // function meta()

	public function render()
	{
		$this->_container_engine->assign($this->_prop);
		$this->_container_engine->content = $this->fetch();
		$this->_container_engine->render();
	} // function render()

} // class View_Container
