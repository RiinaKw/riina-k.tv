<?php

function smarty_modifier_indent2($strInput, $nWidth=1, $indentString="\t")
{
	if ( $nWidth < 0 )
		return $strInput;
	
	$strIndent = str_repeat( $indentString, $nWidth );
	$strInput = str_replace( "\n", ( "\n" . $strIndent ), $strInput );
	return $strInput;
}

?>
