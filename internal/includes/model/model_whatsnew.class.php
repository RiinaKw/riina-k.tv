<?php

require_once(INCLUDE_DIR . '/model/model.class.php');

class Model_Whatsnew extends Model {
	
	function get_all()
	{
		$statement = $this->dbh->prepare('SELECT * FROM whatsnews ORDER BY date DESC;');
		$statement->execute();
		return $statement;
	}
	
} // class Model_Whatsnew
