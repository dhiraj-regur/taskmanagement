<?php
/*śćźżąłóć*/
#########################################################################################################
#DATABASE
function pureText($str) {
	//echo "PRZED:".$str;
	$bad = array("\"", "\'", "&", "$", "*", "$", "?", "!");
	$str = str_replace($bad, "", $str);
	$str = mysql_escape_string($str);
	$str = strip_tags($str);
	$str = stripslashes($str);
	$str = stripslashes($str);
	//echo "PO:".$str;
	return $str;
}
function prepareLink($str) {
	if(isset($str) && trim($str)!="") {
		$str = mysql_escape_string($str);
		$str = strip_tags($str);
		return $str;
	} else {
		return "";
	}
}
function prepareForDb($str) {
	if (get_magic_quotes_gpc() ) {
		$str = trim(strip_tags(htmlspecialchars(stripslashes($str))));
	} else {
		$str = trim(strip_tags(htmlspecialchars($str)));
	}
	return $str;
}
function prepareForDbSimple($str) {
	if (get_magic_quotes_gpc() ) {
		$str = trim(strip_tags(stripslashes($str)));
	} else {
		$str = trim(strip_tags($str));
	}
	return $str;
}
function create_sql() {
	$args = func_get_args();
	if(!is_array($args[0])) return "";
	if(empty($args[0])) return "";
	$params = $args[0];
	$arr = array();
	$temp = array();
	$add_set = array();
	$str = "WHERE ";
	foreach($params as $key=>$value) {
		if($value!==NULL) {
			$str .= $key."=? AND ";
			$temp[] = $value;
			$add_set[$key] = $value;
		}
	}
	$str = substr($str, 0, -4);
	$arr[] = $str;
	$arr[] = $temp;
	$arr[] = $add_set;
	return $arr;
}
function remove_cond_sql($sql, $delete_cond) {	
	if(!is_array($sql[2])) return "";
	if(empty($sql[0])) return "";
	$new_sql = array();
	$add_set = array();
	$temp = array();
	unset($sql[2][$delete_cond]);
	$str = "WHERE ";
	foreach($sql[2] as $key=>$value) {
		if($value!==NULL) {
			$str .= $key."=? AND ";
			$temp[] = $value;
			$add_set[$key] = $value;
		}
	}
	$str = substr($str, 0, -4);
	$new_sql[] = $str;
	$new_sql[] = $temp;
	$new_sql[] = $add_set;
	return $new_sql;
}
?>