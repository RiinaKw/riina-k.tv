<?php

require_once(INCLUDE_DIR . '/view/view.class.php');

class HttpException extends Exception {
	
	protected $title = '';
	protected $code = 0;
	
	public function __construct($message = null, $code = 0)
	{
		if (!$message) {
			throw new $this('Unknown '. get_class($this));
		}
		parent::__construct($message, $code);
	}
	
	public function __toString()
	{
		global $config;
		
		header( 'HTTP/1.0 ' . $this->code . ' ' . $this->title );
		
		if ($config['env'] == 'production') {
			return $this->code . ' ' . get_class($this) . ' ' . $this->title . " '{$this->message}'";
		} else {
			return $this->code . ' ' . get_class($this) . ' ' . $this->title . " '{$this->message}' in {$this->file}({$this->line})\n" . "{$this->getTraceAsString()}";
		}
	}
	
	public function render()
	{
		global $config;
		header( 'HTTP/1.0 ' . $this->code . ' ' . $this->title );
		
		$view = new View('error.tpl.html');
		$view->assignByRef('title', $this->title);
		
		if ($config['env'] == 'production') {
			$view->assignByRef('message', $this->message);
		} else {
			$view->assign('message', $this->message . " in {$this->file}({$this->line})\n" . "{$this->getTraceAsString()}");
		}
		$view->render();
	}
	
}

class HttpBadRequestException extends HttpException {
	
	public function __construct($message = null, $code = 400)
	{
		$this->title = 'Bad Request';
		$this->code = 400;
		parent::__construct($message, $code);
	}
	
}

class HttpForbiddenException extends HttpException {
	
	public function __construct($message = null, $code = 403)
	{
		$this->title = 'Forbiddent';
		$this->code = 403;
		return parent::__construct($message, $code);
	}
	
}

class HttpNotFoundException extends HttpException {
	
	public function __construct($message = null, $code = 404)
	{
		$this->title = 'Not Found';
		$this->code = 404;
		parent::__construct($message, $code);
	}
	
}

class HttpImTeapotException extends HttpException {
	
	public function __construct($message = null, $code = 418)
	{
		$this->title = 'I\'m a teapot';
		$this->code = 418;
		parent::__construct($message, $code);
	}
	
}

class HttpInternalServerErrorException extends HttpException {
	
	public function __construct($message = null, $code = 500)
	{
		$this->title = 'Internal Server Error';
		$this->code = 500;
		parent::__construct($message, $code);
	}
	
}

class HttpNotImplementedException extends HttpException {
	
	public function __construct($message = null, $code = 500)
	{
		$this->title = 'Not Implemented';
		$this->code = 500;
		parent::__construct($message, $code);
	}
	
}
