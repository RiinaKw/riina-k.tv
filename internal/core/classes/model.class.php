<?php

class Model {
	
	protected $dbh;
	
	public function __construct($config)
	{
		$this->dbh = new PDO($config['dsn'], $config['username'], $config['password']);
	} // function __construct()
	
} // class Model