<?php
/*śćółąźćń*/
##################################################################
#TEXT
function is_mail($mail) {
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
function has_text($str) {
	if(trim($str)=="") {
		return false;
	} else {
		return true;
	}
}
# zwraca aktualny wirtualny folder ze ¶cie¿ki HTTP
function getHF() {
	$pos  = strpos($_SERVER['REQUEST_URI'], "?");
	if($pos==false) {
		return basename($_SERVER['REQUEST_URI']);
	} else {	
		$str = substr($_SERVER['REQUEST_URI'], 0, $pos);
		return basename($str);
	}
}
# zwraca pe³n± scie¿kê HTTP aktualnego wirtualnego 
function getFHF() {
	$pos  = strpos($_SERVER['REQUEST_URI'], "?");
	if($pos==false) {
		return $_SERVER['REQUEST_URI'];
	} else {	
		$str = substr($_SERVER['REQUEST_URI'], 0, $pos);
		return $str;
	}
}

function getTHF() {
	global $ACONF;
	if(isset($ACONF['cmset']['href'])) {
		return 	$ACONF['cmset']['href'];
	} else {
		return FHF;	
	}
}

function this_script($file) {
	$break = explode('/', $file);
	$pfile = $break[count($break) - 1];
	return $pfile;
}
function this_script_bare($file) {
	$break = explode('/', $file);
	$pfile = $break[count($break) - 1];
	if(substr($pfile, -4) == ".php" ) {
		$pfile = substr($pfile, 0, -4);
	}
	return $pfile;
}
function is_valid_url($url) {
	return preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $url);
}
#uznaje tak¿e linki wzglêdne
function is_valid_url_less($url) {
	return preg_match('|^[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $url);
}
function removeDoubleNewlines($string) {
	$string=preg_replace('`[\r\n]+`',"\n",$string);	
	return $string;
}
function removeNewlines($str) {
	$order   = array("\r\n", "\n", "\r");
	$str = str_replace($order,"", $str);	
	return $str;
}
function minify_css($str) {
	$str = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $str);
	$str = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $str);
	return $str;
}

function ee($val, $name="", $text_align="left") {
	if(is_array($val)) {
		echo '<div style="text-align:'.$text_align.';">';
		echo "$name: <pre>";
		print_r($val);
		echo "</pre>";
		echo "</div>";	
	} elseif(is_object($val)) {
		echo '<div style="text-align:'.$text_align.';">';
		echo "$name: <pre>";
		print_r($val);
		echo "</pre>";
		echo "</div>";	
	} else {
		echo '<div style="text-align:'.$text_align.';">';
		echo "$name: ".$val;
		echo "</div>";
	}
}
function ea($val, $name="", $text_align="left") {
	echo '<div style="text-align:'.$text_align.';">';
	echo "$name: <pre>";
	print_r($val);
	echo "</pre>";
	echo "</div>";	
}
function testing($data, $file, $clear=false) {
	$nazwa_pliku = $file;
	$data .= " - ";
	$data .= date("Y-m-d H:i:s");
	$data .= "\n";
	$uchwyt = fopen($nazwa_pliku, 'a');
	if($clear) {
		ftruncate($uchwyt, 0);
	}
	fwrite($uchwyt, $data);
	fclose($uchwyt);
}
function generateId(){
	$idFile = date("ymdhis");
	//return base64_encode ($idFile);
	return $idFile;
}
function generate_real_id() {
	$str = microtime();
	return base64_encode ($str);
}
function file_size_format($size){
	if($size>0) {
		$filesizename = array(" Bytes", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB");
		return $size ? round($size/pow(1024, ($i = floor(log($size, 1024)))), 2) . $filesizename[$i] : '0 Bytes';
	} else {
		return -1;
	 }
}
############################################################
### ukośnbiki
function trim_slashes($str) {
	if(substr($str, -1) == "/") {
		$str = substr($str, 0, -1);
	}
	if(substr($str, 0, 1) == "/") {
		$str = substr($str, 1);
	}
	return $str;	
}
function trim_first_slash($str) {
	if(substr($str, 0, 1) == "/") {
		$str = substr($str, 1);
	}	
	return $str;
}
function trim_last_slash($str) {
	if(substr($str, -1) == "/") {
		$str = substr($str, 0, -1);
	}	
	return $str;
}
function add_last_slash($str) {
	if(substr($str, -1)!='/') {
		$str = $str.'/';	
	}
	return $str;
}
############################################################
#
#odmina rzeczownika przez liczbę, jak w zdaniu: masz 1 jabłko
# @1 - liczba o ktorą chodzi
# @2 - jesli to zero
# @3 - jesli to jeden
# @4 jesli to 2, 3, 4
function getDeclination($num, $zero_type, $one_type, $two_type) {
	$str = "";
	#koncowka
	$ending = substr($num, -1);
	if(($ending=="2" || $ending=="3" || $ending=="4") && ($num!==12 && $num!==13 && $num!==14)) {
		$str = $two_type;	
	} else {
		$str = $zero_type;	
	}
	if($num==0) {
		$str = $zero_type;	
	}
	if($num==1) {
		$str = $one_type;	
	}	
	return $str;
}
#dosotosowuje teskt z bazy danych do wyswietlenia w jako input value
function get_db_to_input($str) {
	if(get_magic_quotes_gpc()) {
		$str = stripslashes($str);	
	}
	$str = htmlspecialchars($str);	
	return $str;
}
#dosotosowuje teskt z bazy danych do wyswietlenia w jako input value
function get_db_to_normal($str) {
	if(get_magic_quotes_gpc()) {
		$str = stripslashes($str);	
	}
	return $str;
}
function real_unserialize($serial_str) {
	$serial_arr = unserialize($serial_str);
	$serial_arr = array_stripslashes_if($serial_arr);	
	return $serial_arr;
}
function mail_input_right($str) {
	$str = trim($str);
	$str = strip_tags($str);
	if(get_magic_quotes_gpc())	$str =  stripslashes($str);
	$str = nl2br($str);
	return $str;
}
function mail_textarea_right($str) {
	$str = trim($str);
	$str = strip_tags($str);
	if(get_magic_quotes_gpc())	$str =  stripslashes($str);
	$str = nl2br($str);
	return $str;
}
function str_starts_with($source, $prefix){
   return strncmp($source, $prefix, strlen($prefix)) == 0;
}
function get_str_till_char($str, $char) {
	$pos = strpos($str, $char);
	if ($pos === false) {
		return "";
	} else {
		$part = substr($str, 0, $pos);
	return $part;
	}
}
function iconv_recursevly_iso_utf($data) {
	if(!is_array($data)) {
		return 	iconv("iso-8859-2", "utf-8", $data);
	} elseif(is_array($data)) {
		foreach($data as $key=>$value) {
			$data[$key] = iconv_recursevly_iso_utf($value);
		}	
	}
	return $data;
}
function iconv_recursevly_utf_iso($data) {
	if(!is_array($data)) {
		return 	iconv("utf-8","iso-8859-2", $data);
	} elseif(is_array($data)) {
		foreach($data as $key=>$value) {
			$data[$key] = iconv_recursevly_utf_iso($value);
		}	
	}
	return $data;
}
function var_name (&$iVar, &$aDefinedVars){
    foreach ($aDefinedVars as $k=>$v)
        $aDefinedVars_0[$k] = $v;
    $iVarSave = $iVar;
    $iVar     =!$iVar;
    $aDiffKeys = array_keys (array_diff_assoc ($aDefinedVars_0, $aDefinedVars));
    $iVar      = $iVarSave;
    return $aDiffKeys[0];
}
function get_e($str, $line, $obj) {
	if(!get_class($obj)) {
		return $str." (".$line.")";	
	} else {
		return $str." (".get_class($obj).", ".$line.")";
	}
}
#taguje taga
function tag_me($tag) {
	return '{'.$tag.'}';
}
function remove_apos($str) {
	$str = trim($str);
	if(get_magic_quotes_gpc())	$str =  stripslashes($str);
	$str = str_replace("'", '', $str);
	$str = str_replace('"', '', $str);
	return $str;	
}
function _p($var) {
	echo $var;
}
#zwraca tablice z treścimi z formualrza
function get_undone($f) {
	if(!empty($f)) {
		foreach($f as $key=>$value) {
			$f[$key] = 	get_db_to_input($value);
		}	
	}
	return $f;
}
function clear_if_question($str) {
	if(substr($str, 0, 1)=="?" && strlen($str)==1) {
		$str = substr($str, 1);	
	}
	return $str;
}
function generate_pass($length=9, $strength=4) {
	$vowels = 'aeuy';
	$consonants = 'bdghjmnpqrstvz';
	if ($strength & 1) {
		$consonants .= 'BDGHJLMNPQRSTVWXZ';
	}
	if ($strength & 2) {
		$vowels .= "AEUY";
	}
	if ($strength & 4) {
		$consonants .= '23456789';
	}
	if ($strength & 8) {
		$consonants .= '@#$%';
	}
 
	$password = '';
	$alt = time() % 2;
	for ($i = 0; $i < $length; $i++) {
		if ($alt == 1) {
			$password .= $consonants[(rand() % strlen($consonants))];
			$alt = 0;
		} else {
			$password .= $vowels[(rand() % strlen($vowels))];
			$alt = 1;
		}
	}
	return $password;
}
function convert_decimal_from_pl_to_en($str) {
	$str = str_replace('.', '', $str);
	$str = str_replace(',', '.', $str);
	return $str;
}
function convert_decimal_from_pl_to_en2($str) {
	$str = str_replace(',', '.', $str);
	return $str;
}
function convert_decimal_from_en_to_pl($str) {
	$str = str_replace('.', ',', $str);
	return $str;
}

?>