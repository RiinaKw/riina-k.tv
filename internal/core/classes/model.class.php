<?php

class Model
{

	protected $_dbh;

	public function __construct()
	{
		global $bootstrap;

		$this->_dbh = $bootstrap->dbh;
	} // function __construct()

} // class Model
