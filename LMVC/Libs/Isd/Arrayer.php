<?php
class Isd_Arrayer {
    public static function real_explode($del, $str) {
		$set = explode($del, $str);
		if(is_array($set)) {
			foreach($set as $key=>$value) {
				if(!Isd_Texter::has_text($value)) {
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
	
	public static function simple_array_me($arr, $field) {
		if(!is_array($arr) || empty($arr)) return array();
		if($field=='' || empty($field)) return array();
		$new_arr = array();
		foreach($arr as $value) {
			$new_arr[] =  $value[$field];	
		}
		return $new_arr;
	}
	
	public static function create_date_range_array($strDateFrom, $strDateTo) {
	  // takes two dates formatted as YYYY-MM-DD and creates an
	  // inclusive array of the dates between the from and to dates.
	
	  // could test validity of dates here but I'm already doing
	  // that in the main script
	
	  $aryRange=array();
	
	  $iDateFrom=mktime(1,0,0,substr($strDateFrom,5,2),     substr($strDateFrom,8,2),substr($strDateFrom,0,4));
	  $iDateTo=mktime(1,0,0,substr($strDateTo,5,2),     substr($strDateTo,8,2),substr($strDateTo,0,4));
	
	  if ($iDateTo>=$iDateFrom) {
		array_push($aryRange,date('Y-m-d',$iDateFrom)); // first entry
	
		while ($iDateFrom<$iDateTo) {
		  $iDateFrom+=86400; // add 24 hours
		  array_push($aryRange,date('Y-m-d',$iDateFrom));
		}
	  }
	  return $aryRange;
	}
	
	public static function iconv_recursevly_iso_utf($data) {
		if(!is_array($data)) {
			return 	iconv("iso-8859-2", "utf-8", $data);
		} elseif(is_array($data)) {
			foreach($data as $key=>$value) {
				$data[$key] = iconv_recursevly_iso_utf($value);
			}	
		}
		return $data;
	}
	
	public static function iconv_recursevly_utf_iso($data) {
		if(!is_array($data)) {
			return 	iconv("utf-8","iso-8859-2", $data);
		} elseif(is_array($data)) {
			foreach($data as $key=>$value) {
				$data[$key] = iconv_recursevly_utf_iso($value);
			}	
		}
		return $data;
	}
	
	public static function order_by($data, $field, $mode="ASC") { 
		$code = "return strnatcmp(\$a['$field'], \$b['$field']);"; 
		usort($data, create_function('$a,$b', $code)); 
		
		if($mode=="DESC") {
			$data = array_reverse($data);
		}
		
		return $data; 
	}
}