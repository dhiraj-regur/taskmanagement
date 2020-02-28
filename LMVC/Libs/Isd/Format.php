<?php
class Isd_Format {
    public static function nullFromDb($str, $default='b/d') {
		if(is_null($str)) $str = $default;
		return $str;
	}
	
	public static function convert_decimal_from_en_to_pl($str) {
		$str = str_replace('.', ',', $str);
		return $str;
	}
}