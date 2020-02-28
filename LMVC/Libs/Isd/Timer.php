<?php
class Isd_Timer {
	public static function getMicrotime(){ 
		list($usec, $sec) = explode(" ",microtime()); 
		return ((float)$usec + (float)$sec); 
	}	
}
?>