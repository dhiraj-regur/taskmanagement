<?php
/*śćźżąłóć*/
############################################################################################################
#TIME
function getMonthsNum() {
	return array("01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12");
}
function getMonthsNames() {
	return array("styczeń", "luty", "marzec", "kwiecień", "maj", "czerwiec", "lipiec", "sierpień", "wrzesień", "październik", "listopad", "grudzień");
}
function getHours() {
	return array("01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12","13", "14", "15", "16", "17", "18", "19", "20", "21", "22", "23", "24");
}
function getDays() {
	return array("01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12","13", "14", "15", "16", "17", "18", "19", "20", "21", "22", "23", "24", "25", "26", "27", "28", "29", "30", "31");
}
function getNearYears($past, $future, $reverse=false) {
	$current_y = date("Y");
	$first_y = $current_y-$past;
	$last_y = $current_y+$future;
	$years = array();
	
	$k=0;
	for($i=$first_y; $i<=$last_y;$i++) {
		$years[$k]['num']=$i;
		if($i==$current_y) {
			$years[$k]['current']=1;
		} else {
			$years[$k]['current']=0;
		}
		
		$k++;	
	}
	
	if($reverse) {
		rsort($years);
	}
	
	return $years;
}
function getNearYearsCurrent ($past, $future, $current) {
	$current_y = date("Y");
	$first_y = $current_y-$past;
	$last_y = $current_y+$future;
	$years = array();
	
	$k=0;
	for($i=$first_y; $i<=$last_y;$i++) {
		$years[$k]['num']=$i;
		if($i==$current) {
			$years[$k]['current']=1;
		} else {
			$years[$k]['current']=0;
		}
		
		$k++;	
	}
	return $years;
}
function getMinutes() {
	$minutes = array();
	for($i=0; $i<=60;$i++) {
		if($i<10) {
		$value = "0".$i;
		} else {
		$value = $i;
		}
		array_push($minutes, $value);
	} 
	return $minutes;
}
function getMonthsArr() {
	$months_names = getMonthsNames();
	$months_num = getMonthsNum();
	$months = array();
	foreach ($months_num as $key=>$value) {
		$months[$key]['num'] = $value;
		$months[$key]['name'] = $months_names[$key];
	}
	return $months;
}
function getMonthsCurrentArr($current) {
	$months_names = getMonthsNames();
	$months_num = getMonthsNum();
	$months = array();
	foreach ($months_num as $key=>$value) {
		$months[$key]['num'] = $value;
		$months[$key]['name'] = $months_names[$key];
		if($current==$value) {
			$months[$key]['current'] = 1;
		} else {
			$months[$key]['current'] = 0;
		}
	}
	return $months;
}
function getDaysCurrent($current) {
	$days = getDays();
	$arr = array();
	
	foreach($days as $key=>$value) {
		$arr[$key]['num'] =  $value;
		
		if($value==$current) {
			$arr[$key]['current'] =  1;
		} else {
			$arr[$key]['current'] =  0;
		}
	}
	
	return $arr;
}
function explode_date($date) {
	$arr = array();
	
	$y = substr($date, 0, 4);
	$m = substr($date, 5, 2);
	$d = substr($date, 8, 2);
	
	if(intval($y)>0 && intval($m)>0 && intval($d)>0) {
		$arr['y'] = $y;
		$arr['m'] = $m;
		$arr['d'] = $d;
		return $arr;
	} else {
		return array();
	}
}
function getmicrotime(){ 
	list($usec, $sec) = explode(" ",microtime()); 
	return ((float)$usec + (float)$sec); 
}
function get_time_difference( $start, $end ) {
	$uts = array();
    $uts['start']      =    strtotime( $start );
    $uts['end']        =    strtotime( $end );
    if( $uts['start']!==-1 && $uts['end']!==-1 ) {
        if( $uts['end'] >= $uts['start'] ) {
            $diff    =    $uts['end'] - $uts['start'];
            if( $days=intval((floor($diff/86400))) )
                $diff = $diff % 86400;
            if( $hours=intval((floor($diff/3600))) )
                $diff = $diff % 3600;
            if( $minutes=intval((floor($diff/60))) )
                $diff = $diff % 60;
            $diff    =    intval( $diff );            
            return( array('days'=>$days, 'hours'=>$hours, 'minutes'=>$minutes, 'seconds'=>$diff) );
        } else {
            trigger_error( "Ending date/time is earlier than the start date/time", E_USER_WARNING );
        }
    } else {
        trigger_error( "Invalid date/time data detected", E_USER_WARNING );
    }
    return (false);
}

function get_date_difference($date_2, $date_1) {			
	$diff = abs(strtotime($date2) - strtotime($date1));
				
	$years = floor($diff / (365*60*60*24));
	$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
	$days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
	
	$arr =  array();
	$arr['years'] = $years;
	$arr['months'] = $months;
	$arr['days'] = $days;
	return $arr;
}

/* Works out the time since the entry post, takes a an argument in unix time (seconds) */
function time_since($original) {
    // array of time period chunks
    $chunks = array(
        array(60 * 60 * 24 * 365 , 'year'),
        array(60 * 60 * 24 * 30 , 'month'),
        array(60 * 60 * 24 * 7, 'week'),
        array(60 * 60 * 24 , 'day'),
        array(60 * 60 , 'hour'),
        array(60 , 'minute'),
    );
    
    $today = time(); /* Current unix time  */
    $since = $today - $original;
    
    // $j saves performing the count function each time around the loop
    for ($i = 0, $j = count($chunks); $i < $j; $i++) {
        
        $seconds = $chunks[$i][0];
        $name = $chunks[$i][1];
        
        // finding the biggest chunk (if the chunk fits, break)
        if (($count = floor($since / $seconds)) != 0) {
            // DEBUG print "<!-- It's $name -->\n";
            break;
        }
    }
    
    $print = ($count == 1) ? '1 '.$name : "$count {$name}s";
    
    if ($i + 1 < $j) {
        // now getting the second item
        $seconds2 = $chunks[$i + 1][0];
        $name2 = $chunks[$i + 1][1];
        
        // add second item if it's greater than 0
        if (($count2 = floor(($since - ($seconds * $count)) / $seconds2)) != 0) {
            $print .= ($count2 == 1) ? ', 1 '.$name2 : ", $count2 {$name2}s";
        }
    }
    return $print;
}
?>