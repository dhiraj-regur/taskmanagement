<?php
class Isd_Smarter {
	public static function truncate($string, $length = 80, $etc = '...', $break_words = false, $middle = false) {
		if ($length == 0)
			return '';
		if (strlen($string) > $length) {
			$length -= strlen($etc);
			if (!$break_words && !$middle) {
				$string = preg_replace('/\s+?(\S+)?$/', '', substr($string, 0, $length+1));
			}
			if(!$middle) {
				return substr($string, 0, $length).$etc;
			} else {
				return substr($string, 0, $length/2) . $etc . substr($string, -$length/2);
			}
		} else {
			return $string;
		}
	}
	/*  "%d-%m-%Y %H:%M" */
	public static function date_format($string, $format="%b %e, %Y", $default_date=null){
		if (substr(PHP_OS,0,3) == 'WIN') {
			$hours = strftime('%I', $string);
			$short_hours = ( $hours < 10 ) ? substr( $hours, -1) : $hours; 
			$_win_from = array ('%e',  '%T',       '%D',        '%l');
			$_win_to   = array ('%#d', '%H:%M:%S', '%m/%d/%y',  $short_hours);
			$format = str_replace($_win_from, $_win_to, $format);
		}
		if($string != '') {
			//return strftime($format, smarty_make_timestamp($string));
			$time = iconv("ISO-8859-2","UTF-8", strftime($format, self::make_timestamp($string)) );
			return $time;
		} elseif (isset($default_date) && $default_date != '') {
			return strftime($format, self::make_timestamp($default_date));
		} else {
			return;
		}
	}
	
	public static function make_timestamp($string){
		if(empty($string)) {
			// use "now":
			$time = time();
		} elseif (preg_match('/^\d{14}$/', $string)) {
			// it is mysql timestamp format of YYYYMMDDHHMMSS?            
			$time = mktime(substr($string, 8, 2),substr($string, 10, 2),substr($string, 12, 2),
						   substr($string, 4, 2),substr($string, 6, 2),substr($string, 0, 4));
		} elseif (is_numeric($string)) {
			// it is a numeric string, we handle it as timestamp
			$time = (int)$string;
		} else {
			// strtotime should handle it
			$time = strtotime($string);
			if ($time == -1 || $time === false) {
				// strtotime() was not able to parse $string, use "now":
				$time = time();
			}
		}
		return $time;
	}
}