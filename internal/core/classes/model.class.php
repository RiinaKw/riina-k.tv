<?php

class Model {
	
	protected $_dbh;
	
	public function __construct($config)
	{
		$this->_dbh = new PDO($config['dsn'], $config['username'], $config['password']);
	} // function __construct()
	
} // class Model
