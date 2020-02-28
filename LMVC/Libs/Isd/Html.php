<?php
abstract class Isd_Html {
	function __construct() {
	   
	}
	
	#tworzy string atrybutów z podanej tablicy
	public function attributes($arr=array()) {
    	if(empty($arr) || !is_array($arr) || count($arr)<0) return '';
    	$html = '';
        $tmp =  array();
        foreach($arr as $key=>$value) {
        	$tmp[] = trim($key).'="'.trim($value).'"';
       	}
    	$html = implode(' ', $tmp);
		return $html;
   	}
	
	public function tabs($num) {
		$str = "";
		if(is_numeric($num) && $num>0) {
			for($i = 0; $i < $num; $i ++ ) {
				$str .= "\t";
			}	
		}
		return $str;
	}
}