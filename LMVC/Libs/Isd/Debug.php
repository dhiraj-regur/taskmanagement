<?php
class Isd_Debug {
    public static function ee($val, $name="", $text_align="left") {
		if(is_array($val)) {
			echo '<div style="text-align:'.$text_align.';">';
			echo "$name: <pre>";
			print_r($val);
			echo "</pre>";
			echo "</div>";	
		} elseif(is_object($val)) {
			echo '<div style="text-align:'.$text_align.';">';
			echo "$name: <pre>";
			print_r($val);
			echo "</pre>";
			echo "</div>";	
		} else {
			echo '<div style="text-align:'.$text_align.';">';
			echo "$name: ".$val;
			echo "</div>";
		}
	}
	
	#debuguj jako obiekt
	public static function eo($val, $name="", $text_align="left") {
		if(is_object($val)) {
			echo '<div style="text-align:'.$text_align.';">';
			echo "$name: <pre>";
			print_r($val);
			echo "</pre>";
			echo "</div>";	
		} else {
			echo "to nie jest obiekt";	
		}
	}
	
	#debuguj jako tablice
	public static function ea($val, $name="", $text_align="left") {
		if(is_array($val)) {
			echo '<div style="text-align:'.$text_align.';">';
			echo "$name: <pre>";
			print_r($val);
			echo "</pre>";
			echo "</div>";	
		} else {
			echo "to nie jest tablica";	
		}
	}
}