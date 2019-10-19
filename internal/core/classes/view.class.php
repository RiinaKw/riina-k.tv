<?php

class View
{

	protected $_template;

	protected $_prop = array();

	public function __construct($template = null)
	{
		$this->set_template($template);
	} // function __construct()

	public function set_template($template)
	{
		$this->_template = $template;
	} // function set_template()

	public function  __get($name)
	{
		if ( array_key_exists($name, $this->_prop) ) {
			return $this->_prop[$name];
		} else {
			trigger_error('variable ' . $name . ' is undefined', E_USER_ERROR);
		}
	} // function  __get()

	public function  __set($name, $value)
	{
		$this->_prop[$name] = $value;
	} // function  __set()

	public function render()
	{
		global $bootstrap;

		$path = $bootstrap->view_path($this->_template);

		if ($path) {
			require_once($path);
		} else {
			throw new HttpInternalServerErrorException('view path missing');
		}
	} // function render()

} // class View
