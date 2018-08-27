<?php

class Model_Whatsnew extends Model {
	
	function get_all()
	{
		$statement = $this->_dbh->prepare('SELECT * FROM whatsnews ORDER BY date DESC;');
		$statement->execute();
		return $statement;
	} // function get_all()
	
} // class Model_Whatsnew
