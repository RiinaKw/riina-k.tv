<?php

class Controller_Preview extends Controller_Base
{

	public function action_detail($arg)
	{
		global $bootstrap;

		$name = ( isset($arg[0]) ? $arg[0] : '' );
		$mode = ( isset($arg[1]) ? $arg[1] : '' );

		$extension = '.mp3';
		$suffix = '-V8';

		try {
			if ( !$name ) {
				throw new HttpBadRequestException('track name missing');
			}

			$model = new Model_Track($bootstrap->db);
			$track = $model->get_by_slug($name);
			if ( !$track ) {
				throw new HttpNotFoundException('track "' . $name . '" not exists');
			}

			$filename = $name . $suffix . $extension;
			$path = $bootstrap->user_path('music', $filename);
			if ( !file_exists($path) ) {
				throw new HttpInternalServerErrorException('missing track "' . $name . '"');
			} else {
				$size = filesize($path);
				// output to log
				$log_path = $bootstrap->user_path('log', 'music.log');
				$log = new Model_Log($log_path, 'a');
				$log->append($name, $suffix);

				// output file
				header('Content-type: audio/mp3');
				header('Content-Length: ' . $size);
				if ($mode == 'download') {
					header( 'Content-Disposition: attachment; filename="' . $name . $extension . '"' );
				}
				echo file_get_contents($path);
			}
		} catch (HttpException $e) {

			$e->render();
			exit;

		}
	} // function action_detail()

} // class Controller_Preview
