<?php

function smarty_modifier_serialize($input)
{
	$input = str_replace(array("\r\n", "\r", "\n"), '', $input);
	
	while ( preg_match('/\[\[(.*?)\]\]/', $input, $matches) ) {
		$arr = explode('|', $matches[1]);
		$url = $arr[0];
		$title = '';
		if ( isset($arr[1]) ) {
			$title = $arr[1];
		} else {
			$title = $url;
		}
		$dest = $title;
		$input = str_replace( $matches[0], $dest, $input );
	}
	
	return $input;
}

?>
