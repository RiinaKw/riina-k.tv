<?php

class Image {

	protected $resource = null;
	protected $filepath = null;
	protected $_prop = [];
	
	protected $_temp_dir = '';
	protected $_temp_prefix = '';
	
	public function __construct($path = '', $temp_dir = '/tmp', $temp_prefix = '')
	{
		if ($path) {
			$this->load($path);
		}
		$this->_temp_dir = $temp_dir;
		$this->_temp_prefix = $temp_prefix;
	} // function __construct()
	
	public function __destruct()
	{
		if ($this->resource) {
			imagedestroy($this->resource);
			$this->resource = null;
		}
	} // function __destruct()
	
	public function __get($name)
	{
		if ( array_key_exists($name, $this->_prop) ) {
			return $this->_prop[$name];
		} else {
			return null;
		}
	} // function __get()
	
	public function create($width, $height = null)
	{
		if ( !$height ) {
			$height = $width;
		}
		$this->resource = imagecreatetruecolor($width, $height);
		// png transparent
		imagealphablending($this->resource, false);
		imagesavealpha($this->resource, true);
		
		$this->_prop['width'] = $width;
		$this->_prop['height'] = $height;
		return $this;
	} // function create()
	
	public function load($path)
	{
		if ( !is_file($path) ) {
			throw new HttpNotFoundException('file not exists');
		}
		$fileinfo = getimagesize($path);
		$this->filepath = $path;
		$this->_prop['mimetype'] = $fileinfo['mime'];
		$this->_prop['width'] = $fileinfo[0];
		$this->_prop['height'] = $fileinfo[1];
		$this->_prop['imagetype'] = $fileinfo[2];
		
		switch ($this->_prop['imagetype']) {
			case IMAGETYPE_JPEG:
			$this->resource = imagecreatefromjpeg($path);
			break;
		case IMAGETYPE_PNG:
			$this->resource = imagecreatefrompng($path);
			break;
		case IMAGETYPE_GIF:
			$this->resource = imagecreatefromgif($path);
			break;
		default:
			throw new HttpNotImplementedException( 'Unknown image type : ' . image_type_to_mime_type($this->imagetype) );
		}
		return $this;
	} // function load()
	
	/****
		one param :
			resample(100) : width as 100px, save the ratio
			resample('50%') : width as 50% of original, save the ratio
			resample('max500') : larger width or height as 500px, save the ratio
			resample('min200') : smaller width or height as 200px, save the ratio
			
		two param :
			resample('', '') : no change
			resample('auto', 'auto') : no change
			resample(100, 'auto') : width as 100px, save the ratio
			resample('50%', 'auto') : width as 50% of original, save the ratio
			resample('auto', 200) : height as 200px, save the ratio
			resample('auto', '50%') : height as 50% of original, save the ratio
			resample(1000, 800) : width as 1000px, height as 800px, NOT save the ratio
			resample('50%', 500) : width as 50% of original, height as 500px, NOT save the ratio
			resample('80%', '150%') : width as 80% of original, height as 150% of original, NOT save the ratio
	****/
	public function resample($new_width, $new_height = null)
	{
		$percentage_width = false;
		$percentage_height = false;
		if( preg_match('/^(.*)%$/', $new_width, $matches) ) {
			$percentage_width = $matches[1] / 100;
		}
		if( preg_match('/^(.*)%$/', $new_height, $matches) ) {
			$percentage_height = $matches[1] / 100;
		}
		
		if ( is_numeric($new_width) && !is_numeric($new_height) ) {
			$new_width = floatval($new_width);
			// width is numeric, height is NOT numeric
			if ( !$new_height || $new_height == 'auto' ) {
				// height is auto
				$percentage = $new_width / $this->_prop['width'];
				$new_height = $this->_prop['height'] * $percentage;
			} else if ( $percentage_height ) {
				// height is percentage
				$new_height = $this->_prop['height'] * $percentage_height;
			} else {
				// unknown height
				throw new HttpBadRequestException('bad height');
			}
		} else if ( !is_numeric($new_width) && is_numeric($new_height) ) {
			$new_height = floatval($new_height);
			// width is NOT numeric, height is numeric
			if ( !$new_width || $new_width == 'auto' ) {
				// width is auto
				$percentage = $new_height / $this->_prop['height'];
				$new_width = $this->_prop['width'] * $percentage;
			} else if ( $percentage_width ) {
				// width is percentage
				$new_width = $this->_prop['width'] * $percentage_width;
			} else {
				// unknown height
				throw new HttpBadRequestException('bad width');
			}
		} else if ( !is_numeric($new_width) && !is_numeric($new_height) ) {
			// width is NOT numeric, height NOT numeric
			if ( ($new_width == '' || $new_width == 'auto') && ($new_height == '' || $new_height == 'auto') ) {
				// both default
			} else if ( preg_match('/^max(.*)$/', $new_width, $matches) && ($new_height == '' || $new_height == 'auto') ) {
				// larger width or height
				$max = ( $this->_prop['width'] > $this->_prop['height'] ? $this->_prop['width'] : $this->_prop['height'] );
				$percentage = $matches[1] / $max;
				$new_width = $this->_prop['width'] * $percentage;
				$new_height = $this->_prop['height'] * $percentage;
			} else if ( preg_match('/^min(.*)$/', $new_width, $matches) && ($new_height == '' || $new_height == 'auto') ) {
				// smaller width or height
				$min = ( $this->_prop['width'] < $this->_prop['height'] ? $this->_prop['width'] : $this->_prop['height'] );
				$percentage = $matches[1] / $min;
				$new_width = $this->_prop['width'] * $percentage;
				$new_height = $this->_prop['height'] * $percentage;
			} else if ( $percentage_width && $percentage_height ) {
				// both percentage
				$new_width = $this->_prop['width'] * $percentage_width;
				$new_height = $this->_prop['height'] * $percentage_height;
			} else if ( $percentage_width && ($new_height == '' || $new_height == 'auto') ) {
				// width is percentage, height is default
				$new_width = $this->_prop['width'] * $percentage_width;
				$new_height = $this->_prop['height'] * $percentage_width;
			} else if ( $percentage_height && ($new_width == '' || $new_width == 'auto') ) {
				// width is default, height is percentage
				$new_width = $this->_prop['width'] * $percentage_height;
				$new_height = $this->_prop['height'] * $percentage_height;
			} else {
				// unknown width or height
				throw new HttpBadRequestException('bad width or height');
			}
		} else {
			// both width and height are numeric
			$new_width = floatval($new_width);
			$new_height = floatval($new_height);
		}
		$new_width = round($new_width);
		$new_height = round($new_height);
		if ( $new_width == 0 || $new_height == 0 ) {
			throw new HttpBadRequestException('zero assigned');
		} else if ( $new_width * $new_height > 1000000 ) {
			throw new HttpBadRequestException('too large');
		}
		
		$canvas = new Image;
		$canvas->create($new_width, $new_height);
		
		imagecopyresampled(
			$canvas->resource, 
			$this->resource,
			0, 0,
			0, 0,
			$canvas->_prop['width'], $canvas->_prop['height'],
			$this->_prop['width'], $this->_prop['height']);
		
		// gif transparent
		$bgcolor = imagecolorallocatealpha($this->resource, 0, 0, 0, 127);
		imagefill($canvas->resource, 0, 0, $bgcolor);
		imagecolortransparent($canvas->resource, $bgcolor);
		
		return $canvas;
	} // function resample()
	
	public function sharp()
	{
		$this->_require_resource();
		
		$matrix  = array(
			array( 0.0, -1.0,  0.0),
			array(-1.0,  9.0, -1.0),
			array( 0.0, -1.0,  0.0)
		); 

		// calculate the sharpen divisor 
		$divisor = array_sum( array_map('array_sum', $matrix ) );
		$offset = 0; 

		// apply the matrix 
		imageconvolution($this->resource, $matrix, $divisor, $offset);
		
		return $this;
	} // function sharp()
	
	public function pixelate($size)
	{
		$this->_require_resource();
		imagefilter($this->resource, IMG_FILTER_PIXELATE, $size, false);
		return $this;
	} // function pixelate()
	
	protected function _tempnam()
	{
		$name = @tempnam($this->_temp_dir, $this->_temp_prefix);
		return $name;
	} // function _tempnam()
	
	protected function _output($filepath, $to = null, $use_self = false)
	{
		// get binary
		if ($use_self) {
			$response = $this;
			$binary = file_get_contents($filepath);
		} else {
			$response = new Image($filepath);
			$binary = file_get_contents($filepath);
			unlink($filepath);
		}
		
		if ($to == 'base64') {
			// base64 encode for embed
			$base64 = 'data:'
				. $response->_prop['mimetype']
				. ';base64,'
				. base64_encode($binary);
			return $base64;
		} else if ($to) {
			// put to file
			return file_put_contents($to, $binary);
		} else {
			// show
			$filesize = strlen($binary);
			header('Content-Type: ' . $response->_prop['mimetype']);
			header('Content-Length: ' . $filesize);
			echo $binary;
			return true;
		}
	} // function _output()
	
	public function thru($to = null)
	{
		if ( $this->filepath && is_file($this->filepath) ) {
			return $this->_output($this->filepath, $to, true);
		} else {
			throw new HttpNotFoundException('file not exists');
		}
	} // function thru()
	
	protected function _require_resource()
	{
		if ( !$this->resource ) {
			throw new HttpBadRequestException('resource not created');
		}
	} // function _require_resource()
	
	public function jpeg($quality = 75, $to = null)
	{
		$this->_require_resource();
		$tmp = $this->_tempnam();
		imagejpeg($this->resource, $tmp, $quality);
		return $this->_output($tmp, $to);
	} // function jpeg()
	
	public function gif($to = null)
	{
		$this->_require_resource();
		$tmp = $this->_tempnam();
		imagegif($this->resource, $tmp);
		return $this->_output($tmp, $to);
	} // function gif()
	
	public function png($quality = 6, $to = null)
	{
		$this->_require_resource();
		$tmp = $this->_tempnam();
		imagepng($this->resource, $tmp, $quality);
		return $this->_output($tmp, $to);
	} // function png()
	
} // class Image
