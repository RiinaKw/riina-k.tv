<?php

require_once($config->app_class_dir . '/model/model.class.php');

class Model_Log extends Model {
	
	protected $fp;
	
	public function __construct($path, $mode)
	{
		$this->fp = fopen($path, $mode);
	} // function __construct()
	
	function append($name, $suffix)
	{
		if ( !flock($this->fp, LOCK_EX) ) {
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
		fwrite( $this->fp, implode(', ', $logData)."\n" );
		flock($this->fp, LOCK_UN);
		fclose($this->fp);
	} // function append()
	
} // class Model_Log
