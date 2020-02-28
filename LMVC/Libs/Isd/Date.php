<?php
class Isd_Date {
    public static function is_date($date) {
		$stamp = strtotime($date);
		if (!is_numeric($stamp)) return false;
		 
		//checkdate(month, day, year)
		if ( checkdate(date('m', $stamp), date('d', $stamp), date('Y', $stamp)) ){
			return true;
		}
		return false; 
	}
	
	#parsuje date na prawidlowy format do bazy danych 2012-01-31
	public static function parse_date($date) {
		$stamp = strtotime($date);
		if (!is_numeric($stamp)) return false;
		 
		//checkdate(month, day, year)
		if ( checkdate(date('m', $stamp), date('d', $stamp), date('Y', $stamp)) ){
			return date("Y-m-d", $stamp);
		}
		return false;	
	}
	
	public static function is_time($time) {
		$parts = explode(":", $time);
		if(count($parts) == 2) {
			if($parts[0] < 0 || $parts[0]>24 || $parts[1] <0 || $parts[1]>59) return false;
		} elseif(count($parts) == 3) {
			if($parts[0] < 0 || $parts[0]>24 || $parts[1] <0 || $parts[1]>59 || $parts[2] < 0 || $parts[2]>59) return false;
		} elseif(count($parts) == 1) {
			if($parts[0] < 0 || $parts[0]>24 ) return false;
		} else {
			return false;	
		}
		return true;
	}
	
	public static function remove_time($date_with_time) {
		$strtime = strtotime($date_with_time);
		$date = new DateTime();
		$date->setTimestamp($strtime);
		$str = $date->format('Y-m-d');
		return $str;
	}
	
	public static function add_date($vardate, $added) {
		$data = explode("-", $vardate);
		$date = new DateTime();            
		$date->setDate($data[0], $data[1], $data[2]);
		$date->modify("".$added."");
		$day= $date->format("Y-m-d");
		return $day;    
	}
	
	#czy pierwsza jest pozniejsza od drugiej
	public static function compare_dates ($first, $second) {
		$first = strtotime($first);
		$second = strtotime($second); 
		if ($first > $second) return true;
		return false;
	}
	
	#czy pierwsza jest pozniejsza od drugiej
	public static function is_later_or_same ($first, $second) {
		$first = strtotime($first);
		$second = strtotime($second); 
		if ($first >= $second) return true;
		return false;
	}
	
	public static function is_later ($first, $second) {
		$first = strtotime($first);
		$second = strtotime($second);
		if ($first > $second) return true;
		return false;
	}
	
	#czy pierwsza jest wczesniejsza od drugiej
	public static function is_previous_or_same ($first, $second) {
		$first = strtotime($first);
		$second = strtotime($second); 
		if ($first <= $second) return true;
		return false;
	}
	
	public static function get_overlap_days($date_from, $date_to, $date_from_compare, $date_to_compare) {
		if(!self::is_date($date_from) || !self::is_date($date_to) || !self::is_date($date_from_compare) || !self::is_date($date_to_compare)) return 0;
		$date_a = "";
		$date_b = "";
		//ee($date_from.' '.$date_to.' '.$date_from_compare.' '.$date_to_compare, 'dates');
		if(self::is_later_or_same($date_from, $date_from_compare)) {
			$date_a = $date_from;	
		} else {
			$date_a = $date_from_compare;
		}
		
		if(self::is_previous_or_same($date_to, $date_to_compare)) {
			$date_b = $date_to;	
		} else {
			$date_b = $date_to_compare;
		}
		return self::date_diff($date_a, $date_b);	
	}
	
	#tylko dni, które sa robocze
	public static function get_overlap_days_working($date_from, $date_to, $date_from_compare, $date_to_compare) {
		if(!self::is_date($date_from) || !self::is_date($date_to) || !self::is_date($date_from_compare) || !self::is_date($date_to_compare)) return 0;
		$date_a = "";
		$date_b = "";
		//ee($date_from.' '.$date_to.' '.$date_from_compare.' '.$date_to_compare, 'dates');
		if(self::is_later_or_same($date_from, $date_from_compare)) {
			$date_a = $date_from;	
		} else {
			$date_a = $date_from_compare;
		}
		
		if(self::is_previous_or_same($date_to, $date_to_compare)) {
			$date_b = $date_to;	
		} else {
			$date_b = $date_to_compare;
		}
		return self::date_diff_working($date_a, $date_b);	
	}
	
	#jelsi data pierwsza jest pozniejsza niz data druga - zwraca zero
	public static function date_diff_abs ($d1, $d2) {
		///ee(strtotime($d1), '$d1');
		//ee(strtotime($d2), '$d2');
	   $deduct = strtotime($d2) - strtotime($d1);
	  if($deduct<0) return 0;
	  return round($deduct/86400)+1;
	}
	
	public static function date_diff ($d1, $d2) {
	  return round(abs(strtotime($d2)-strtotime($d1))/86400);
	}
	
	public static function date_diff_working($date_a, $date_b){
		setlocale(LC_TIME, 'pl_PL');
		$num  = self::date_diff_abs($date_a, $date_b);
		if($num==0) return 0;
		
		$working_days_num = 0;
		
		for($i = 0; $i < $num; $i ++) {
			$date =   date('Y-m-d', strtotime("+".$i." days", strtotime( $date_a )));
			$date_num  = date("N", strtotime($date));
			if($date_num!=6 && $date_num!=7) $working_days_num ++; 
			//ee($date_num.' '.$date, 'date_num');
		}
		//ee('----------------');
		return $working_days_num;
	}
}
?>