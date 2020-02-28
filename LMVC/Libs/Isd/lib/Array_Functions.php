<?php
/*śćźżąłóć*/
###################################################################################################################################
#ARRAY TRANSITIONS
function is_full_array($val) {
	if(isset($val) && !empty($val) && is_array($val) && count($val)>0) {
		return true;
	} else {
		return false;
	}
}
function array_unique_no_empty($arr) {
	$new_arr = array();
	$arr = array_unique($arr);
	if(!empty($arr)) {
		foreach($arr as $key=>$value) {
			if(has_text($value)) {
				$new_arr[] = $value;
			}
		}
	}	
	return $new_arr;
} 
//
function multi_array_unique($arr, $field) {
	$new_arr = array();
	if(!empty($arr) && !empty($field)) {
		foreach($arr as $key=>$value) {
			$found = 1;
			foreach($new_arr as $key2=>$value2) {
			
				if($value[$field]==$value2[$field]) {
					$found = 2;
				}	
			}
			
			if($found==1) {
				$new_arr[] = $value;
			}
		}
	}	
	return $new_arr;
}
function multi_array_unique_no_empty($arr, $field) {
	$new_arr = array();
	if(!empty($arr) && !empty($field)) {
		foreach($arr as $key=>$value) {
			$found = 1;
			foreach($new_arr as $key2=>$value2) {
			
				if(has_text($value[$field]) && $value[$field]==$value2[$field]) {
					$found = 2;
				}	
			}
			
			if($found==1 && has_text($value[$field])) {
				$new_arr[] = $value;
			}
		}
	}	
	return $new_arr;
}
# sortuje tablice asocjacyjn± przez podane pole
function orderBy($data, $field, $mode="ASC") { 
	$code = "return strnatcmp(\$a['$field'], \$b['$field']);"; 
	usort($data, create_function('$a,$b', $code)); 
	
	if($mode=="DESC") {
		$data = array_reverse($data);
	}
	
	return $data; 
}  
# sortuje przez przypisanie kluczy na nowo
function reset_keys($data) {
	$new_arr = array();
	foreach($data as $key=>$value) {
		$new_arr[] = $value;
	}
	return $new_arr;
}
function has_no_double_values($arr) {
	$values_arr = array();
	foreach($arr as $key=>$value) {
		if(in_array($value, $values_arr)) {
			return false;
		}
		$values_arr[] =  $value;	
	}
	return true;
}
#sprawdza czy druga tablica zawiera przynajmniej takie klucze jak s± zdefiniowane w 1. tablicy
function check_must_keys($must_keys, $given) {
	if(!is_array($must_keys) || !is_array($given)) {
		return false;
	}
	$given_keys = array_keys($given);
	foreach($must_keys as $key=>$value) {
		if(!in_array($value, $given_keys)) {
			return 0;
		}
	}
	
	return 1;
}
function array_htmlspecialchars($arr) {
	if(is_array($arr)) {
		foreach($arr as $key=>$value) {
			if(is_string($value)) {
				$arr[$key] = htmlspecialchars($value);	
			} elseif(is_array($value)) {
				$arr[$key] = array_htmlspecialchars($value);
			}
		}
	}
	return $arr;
}
function array_stripslashes_if($arr) {
	if(is_array($arr) && get_magic_quotes_gpc()) {
		
		foreach($arr as $key=>$value) {
			if(is_string($value)) {
				$arr[$key] = stripslashes($value);	
			} elseif(is_array($value)) {
				$arr[$key] = array_htmlspecialchars($value);
			}
		}
	}
	return $arr;
}
function array_compare($op1, $op2) {
    if (count($op1) < count($op2)) {
        return -1; // $op1 < $op2
    } elseif (count($op1) > count($op2)) {
        return 1; // $op1 > $op2
    }
    foreach ($op1 as $key => $val) {
        if (!array_key_exists($key, $op2)) {
            return null; // uncomparable
        } elseif ($val < $op2[$key]) {
            return -1;
        } elseif ($val > $op2[$key]) {
            return 1;
        }
    }
    return 0; // $op1 == $op2
}
# sortuje tablice assoc wg kluczy z tablicy num na postawie podanego pola
function sort_array_by_other_array($arr, $keys_arr, $num_field) {
	$new_arr = array();
	$left_arr = $arr;
	
	foreach($keys_arr as $key=>$value) {
		foreach($arr as $key2=>$value2) {
			if($value2[$num_field] == $value) {
				$new_arr[] = $value2;
				unset($left_arr[$key2]);
			}	
		}
	}	
	$new_arr = array_merge($new_arr, $left_arr);
	return $new_arr;
}
/*
	@ usuwa powtarzaj±ce siê wato¶ci w tablicy, wliczaj±c w to tablice
*/
function array_unique_super($array, $sort=false) {
	if($sort!=false) {
		sort($array);
	}
  	$result = array_map("unserialize", array_unique(array_map("serialize", $array)));
  	foreach ($result as $key => $value){
    	if ( is_array($value) ) {
    	  	$result[$key] = array_unique_super($value);
    	}
	}
  	return $result;
}
function real_explode($del, $str) {
	$set = explode($del, $str);
	if(is_array($set)) {
		foreach($set as $key=>$value) {
			if(!has_text($value)) {
				unset($set[$key]);	
			}	
		}
		$new_arr = array();
		foreach($set as $key=>$value) {
			$new_arr[] = trim($value);
		}
		return $new_arr;
	} else {
		return array();	
	}
}
# zamienia klucze tablicy na takie jak klucz pola wewnętrznego podany jako parametr
function tranform_by_key($key_name, $arr) {
	if(!is_array($arr)) return array();
	if(empty($arr)) return array();
	$new_arr = array();
	foreach($arr as $key=>$value) {
		$new_arr[$value[$key_name]] =  $value;	
	}
	return $new_arr;
}
function set_stripped($contents) {
	if(!is_array($contents)) return array();
	foreach($contents as $key=>$value) {
		if(isset($value['content'])) {
			$contents[$key]['content_strip'] = strip_tags($value['content']);
		}
	}
	return $contents;
}

function getOtherSettings($settings) {
		if(has_text($settings))	{
			if(get_magic_quotes_gpc()) {
				$serial_arr = array_stripslashes_if(unserialize($settings));	
			} else {
				$serial_arr = unserialize($settings);	
			}
			return $serial_arr;
		}
		return array();
	}
?>