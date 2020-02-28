<?php
class Isd_Mather {
    public static function cycle_values($str, $inter, $no_empty=true) {
		if($no_empty) {
			$arr = Isd_Arrayer::real_explode(';', $str);
		} else {
			$arr = explode(';', $str);	
		}
		//ee($arr);
		if(is_array($arr) && !empty($arr) && is_numeric($inter)) {
			$num = count($arr);
			$remainder = $inter % $num;
			echo $arr[$remainder];
		}
	}
	
	public static  function median($arr, $sort_it=true) {
		#bo byc moze jest juz posortowane z bazy
		if($sort_it) sort($arr);
		
		$count = count($arr);
		$middleval = floor(($count-1)/2);
		if($count % 2) { 
			$median = $arr[$middleval];
		} else {
			$low = $arr[$middleval];
			$high = $arr[$middleval+1];
			$median = (($low+$high)/2);
		}
		return $median;
	}
}