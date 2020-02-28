<?php
/*śćźżąłóć*/

function is_real_numeric($str) {
	if(is_numeric($str) && intval($str)>0) return true;	
	return false;
}
function string_is_numeric($str) {
	$valid_chars = "0123456789";
	$is_number=true;
	for ($i = 0; $i < strlen($str) && $is_number == true; $i++) { 
		$char = $str{$i}; 
		if (strpos($valid_chars, $char) === false) {
			$is_number = false;
		}
	}
	return $is_number; 
}
?>