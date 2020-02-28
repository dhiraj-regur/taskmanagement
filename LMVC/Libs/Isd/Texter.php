<?php
class Isd_Texter {
    public static function is_part_of_string($needle, $haystack) {
		$pos = strpos($haystack, $needle);
		if(false !== $pos) {
			return true;					
		}
		return false;
	}
	
	public static function short_url($str, $allow_slash=false) {
		$alfa = array("a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "w", "v", "x", "y", "z");
		$nums = array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9");
		if($allow_slash) {
			$specials = array(".", "-", "/");
		} else {
			$specials = array(".", "-");
		}
		
		$allowed_chars = array_merge($alfa, $nums, $specials);
		$chars = array(	
			'\''=>'',
			'&'=>'_and_',
			','=>'',
			';'=>'',
			':'=>'',
			'!'=>'',
			'?'=>'',
			'\''=>'',
			'\"'=>'',
			'"'=>'',
			'$'=>'',
			'^'=>'',
			'*'=>'',
			'@'=>'',
			'('=>'',
			')'=>'',
			'<'=>'',
			'>'=>'',
			'['=>'',
			']'=>'',
			'%'=>'proc',
			'ć'=>'c',
			'ś'=>'s',
			'ż'=>'z',
			'ź'=>'z',
			'ą'=>'a',
			'ę'=>'e',
			'ó'=>'o',		
			'ł'=>'l',
			'ń'=>'n',
			' '=>'-'
		);
		$str = mb_strtolower($str, 'UTF-8');
		foreach($chars as $key=>$value) {
			$str = str_replace($key, $value, $str); 
		}
		$new_str = "";
		for($i = 0; $i < strlen($str); $i++) {
			if(in_array( $str[$i], $allowed_chars)) {
				$new_str .=	$str[$i];
			}	
		} 
		if(substr($new_str, 0, 1)==".") {
			$new_str = "_".$new_str;	
		}
		return $new_str;
	}
	
	public static function get_simple_file_name($str, $allow_slash=true) {
			$alfa = array("a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z");
			$nums = array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9");
			
			if($allow_slash) {
				$specials = array(".", "_", "/");
			} else {
				$specials = array(".", "_");
			}
			$allowed_chars = array_merge($alfa, $nums, $specials);
			
			$chars = array(
				' '=>'_',
				'\''=>'',
				'&'=>'_and_',
				','=>'',
				';'=>'',
				':'=>'',
				'!'=>'',
				'?'=>'',
				'\''=>'',
				'\"'=>'',
				'"'=>'',
				'$'=>'',
				'^'=>'',
				'*'=>'',
				'@'=>'',
				'('=>'',
				')'=>'',
				'<'=>'',
				'>'=>'',
				'['=>'',
				']'=>'',
				'%'=>'proc',
				'ć'=>'c',
				'ś'=>'s',
				'ż'=>'z',
				'ź'=>'z',
				'ž'=>'z',
				'ą'=>'a',
				'ę'=>'e',
				'ó'=>'o',		
				'ł'=>'l',
				'ń'=>'n'
			);
			$str = strtolower($str);
			
			foreach($chars as $key=>$value) {
				$str = str_replace($key, $value, $str); 
			}
			
			$new_str = "";
			
			for($i = 0; $i < strlen($str); $i++) {
				if(in_array( $str[$i], $allowed_chars)) {
					$new_str .=	$str[$i];
				}	
			} 
			
			if(substr($new_str, 0, 1)==".") {
				$new_str = "_".$new_str;	
			}
			return $new_str;
	}
	
	public static function has_text($str) {
		if(trim($str)=="") {
			return false;
		} else {
			return true;
		}
	}
	
	public static function _p($var) {
		echo $var;
	}
	
	public static function convert_decimal_from_pl_to_en($str) {
		$str = str_replace('.', '', $str);
		$str = str_replace(',', '.', $str);
		return $str;
	}
	public static function convert_decimal_from_pl_to_en2($str) {
		$str = str_replace(',', '.', $str);
		return $str;
	}
	public static function convert_decimal_from_en_to_pl($str) {
		$str = str_replace('.', ',', $str);
		return $str;
	}
	
	public static function get_last_param($url) {
		$parts = real_explode('/', $url);
		return $parts[count($parts)-1];
	}
	
	public static function is_mail($mail) {
		$wzorzec = '/^([\w\.+-]+)@([0-9a-zA-z\.-]+)\.(\w{2,6})$/' ;
		$do = strip_tags($mail);	
		preg_match($wzorzec, $do, $wynik);
		$wynik = count($wynik);
		if($wynik<4) {
			return false;
		} else {
			return true;
		}
	}
	
	public static function csv($list, $field_to_protect=array()) {
		foreach($list as $key=>$value) {
			$out = '';
			foreach($value as $field=>$val) {
				if(in_array($field, $field_to_protect)) {
					$out .= '"'.addslashes($value)."'";
				} else {
					$out .= $val.",";
				}
			}
			$out .= "\n";
			echo $out;
		}
	}
	
	public static function trim_last_slash($str) {
		if(substr($str, -1) == "/") {
			$str = substr($str, 0, -1);
		}	
		return $str;
	}
	
	public static function trim_last_chars($str, $how_many=1) {
		$str = substr($str, 0, - $how_many);
		return $str;	
	}
	
	public static function generateSalt($len = 16) {
        $dynamicSalt = '';
        for ($i = 0; $i < $len; $i++) {
            $dynamicSalt .= chr(rand(33, 126));
        }
        return $dynamicSalt;
    }
	
	 public static function generateString($length = 32) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
        $string = '';

        $len = strlen($characters)-1;
        for ($i = 0; $i < $length; $i++) {
            $string .= $characters[mt_rand(0, $len)];
        }

        return $string;
    }
}