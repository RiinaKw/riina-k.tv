<?php

function smarty_modifier_makelink($input)
{
	while ( preg_match('/\[\[(.*?)\]\]/', $input, $matches) ) {
		$arr = explode('|', $matches[1]);
		$url = $arr[0];
		$title = '';
		if ( isset($arr[1]) ) {
			$title = $arr[1];
		} else {
			$title = $url;
		}
		$attr = '';
		if ( isset($arr[2]) ) {
			$attrs = explode(',', $arr[2]);
			$dest_attrs = array();
			foreach($attrs as $attr) {
				list($name, $value) = explode(':', $attr);
				$dest_attrs[] = $name . '="' . $value . '"';
			}
			$attr = ' ' . implode(' ', $dest_attrs);
		}
		$dest = '<a href="' . $url . '"' . $attr . '>' . $title . '</a>';
		$input = str_replace( $matches[0], $dest, $input );
	}
	return $input;
}

?>
