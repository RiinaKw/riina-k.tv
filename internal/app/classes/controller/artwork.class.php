<?php

class Controller_Artwork extends Controller_Base {
	
	public function action_detail($arg)
	{
		global $bootstrap;
		
		try {

			$filename = '';
			$filename = ( isset($arg[0]) ? $arg[0] : '' );
			$width    = ( isset($arg[1]) ? $arg[1] : '' );
			$height   = ( isset($arg[2]) ? $arg[2] : '' );
			if ( !$filename ) {
				throw new HttpBadRequestException('file name missing');
			}
			
			if ($filename == 'riina') {
				throw new HttpImTeapotException('おれはやかんだ (Easter Egg)');
			}
			
			$path = $bootstrap->artwork_dir . '/' . $filename;
			if ( !is_file($path) ) {
				throw new HttpNotFoundException('file "' . $filename . '" not exists');
			}
			
			$image = new Image($path);
			//$image->resample($width, $height)->sharp()->png();
			$image->thru();
			
		} catch (HttpException $e) {
			
			$e->render();
			exit;
			
		}
	}
	
}