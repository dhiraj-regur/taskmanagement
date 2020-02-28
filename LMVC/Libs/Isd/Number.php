<?php
class Isd_Number {
	public static function polish_no_decimals($num, $distance=0) {
		$num = round($num);
		return number_format($num,$distance, NULL, ' ');
	}
	
	public static function polish($num, $is_percent=false, $decimals=2) {
		if(is_numeric($num)) {
			$num = number_format($num, $decimals, ',', ' ');
			if($is_percent) {
				$num = $num . ' %';
			}
		}
		return $num;
	}
	
	public static function fraction_to_percent($num, $with_sign=false) {
		if(is_numeric($num)) {
			$num = 100 * $num;
			if($with_sign) $num .= ' %';
		}
		return $num;
	}
	
	public static function polish_fraction_to_percent($num, $with_sign=true, $decimals=2) {
		if(is_numeric($num)) {
			$num = 100 * $num;
			$num = self::polish($num, $with_sign, $decimals);
		}
		return $num;
	}
	
	 public static function null_from_db($num, $default='b/d') {
		if(is_null($num) || empty($num) || $num=='') $num = $default;
		return $num;
	}
	
	public static function pln_to_mld($num) {
		$num = $num/1000000000;
		return number_format($num, 3, ',', ' ');
	}
	
	public static function pln_to_mln($num) {
		$num = $num/1000000000;
		return number_format($num, 3, ',', ' ');
	}
	
	
}
?>