<?php

class HttpException extends Exception {
	
	protected $title = '';
	protected $code = 0;
	
	public function __construct($message = null, $code = 0)
	{
		if (!$message) {
			throw new $this('Unknown '. get_class($this));
		}
		parent::__construct($message, $code);
	} // function __construct()
	
	public function __toString()
	{
		global $bootstrap;
		
		header( 'HTTP/1.0 ' . $this->code . ' ' . $this->title );
		
		if ($bootstrap->env == 'production') {
			return $this->code . ' ' . get_class($this) . ' ' . $this->title . " '{$this->message}'";
		} else {
			return $this->code . ' ' . get_class($this) . ' ' . $this->title . " '{$this->message}' in {$this->file}({$this->line})\n" . "{$this->getTraceAsString()}";
		}
	} // function __toString()
	
	public function render()
	{
		global $bootstrap;
		
		header( 'HTTP/1.0 ' . $this->code . ' ' . $this->title );
		
		$view = new View('error.tpl.php');
		$view->title = $this->code . ' ' . $this->title;
		
		if ($bootstrap->env == 'production') {
			$view->message = $this->message;
			$view->trace = '';
		} else {
			$view->message = $this->message . " in {$this->file}({$this->line})";
			$view->trace = $this->getTraceAsString();
		}
		$view->render();
	} // function render()
	
} // class HttpException

class HttpBadRequestException extends HttpException {
	
	public function __construct($message = null, $code = 400)
	{
		$this->title = 'Bad Request';
		$this->code = 400;
		parent::__construct($message, $code);
	}
	
} // class HttpBadRequestException

class HttpForbiddenException extends HttpException {
	
	public function __construct($message = null, $code = 403)
	{
		$this->title = 'Forbidden';
		$this->code = 403;
		return parent::__construct($message, $code);
	}
	
} // class HttpForbiddenException

class HttpNotFoundException extends HttpException {
	
	public function __construct($message = null, $code = 404)
	{
		$this->title = 'Not Found';
		$this->code = 404;
		parent::__construct($message, $code);
	}
	
} // class HttpNotFoundException

class HttpImTeapotException extends HttpException {
	
	public function __construct($message = null, $code = 418)
	{
		$this->title = 'I\'m a teapot';
		$this->code = 418;
		parent::__construct($message, $code);
	}
	
} // class HttpImTeapotException

class HttpInternalServerErrorException extends HttpException {
	
	public function __construct($message = null, $code = 500)
	{
		$this->title = 'Internal Server Error';
		$this->code = 500;
		parent::__construct($message, $code);
	}
	
} // class HttpInternalServerErrorException

class HttpNotImplementedException extends HttpException {
	
	public function __construct($message = null, $code = 501)
	{
		$this->title = 'Not Implemented';
		$this->code = 501;
		parent::__construct($message, $code);
	}
	
} // class HttpNotImplementedException
