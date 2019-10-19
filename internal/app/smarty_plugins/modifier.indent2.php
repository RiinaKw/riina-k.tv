<?php

function smarty_modifier_indent2($input, $width=1, $indentString="\t")
{
	if ($width < 0) {
		return $input;
	}
	$indent = str_repeat($indentString, $width);
	$input = str_replace("\n", ( "\n" . $indent ), $input);
	return $input;
}
