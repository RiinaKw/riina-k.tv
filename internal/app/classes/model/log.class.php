<?php

class Model_Log extends Model {

	protected $_fp;

	public function __construct($path, $mode)
	{
		$this->_fp = fopen($path, $mode);
	} // function __construct()

	function append($name, $suffix)
	{
		if ( !flock($this->_fp, LOCK_EX) ) {
			throw new HttpInternalServerErrorException('cannot read log file');
		}

		$logData = array(
			date('Y/m/d H:i:s'),
			$name,
			$suffix,
			$_SERVER['REMOTE_ADDR'],
			'"' . gethostbyaddr( $_SERVER['REMOTE_ADDR'] ) . '"',
			'"' . $_SERVER["HTTP_USER_AGENT"] . '"'
		);
		fwrite( $this->_fp, implode(', ', $logData)."\n" );
		flock($this->_fp, LOCK_UN);
		fclose($this->_fp);
	} // function append()

} // class Model_Log
