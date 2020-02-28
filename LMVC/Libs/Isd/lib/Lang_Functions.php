<?php
/*śćłóóżćżń*/
############################################################
# zwraca odpowiednie tłumaczenie z tablicy TRL - DLA ADMINA
# 1 - tekst do tłumacenia
# 2 - tablica jezykowa, która ma być użyta zamiast domyślnej tablicy TRL
# 3 - język - jesli nie można go pobrać ze stałej ISD_ADMIN_USER_LANG - np. w aplikacjach AJAXowych
############################################################
function _tr($str, $added_array = array(), $lang="") {
	global $TRL;
	if(!is_array($added_array)) return;
	if(isset($lang) && $lang!='') define("ISD_ADMIN_USER_LANG", $lang);
	
	if(!defined('ISD_ADMIN_USER_LANG')) return $str;
	
	
	if(count($added_array)==0) {
		if(ISD_ADMIN_USER_LANG == ISD_DEFAULT_LANG) {
			return $str;
		} elseif(isset($TRL[$str])) {
			return $TRL[$str];
		} else {
			return $str;
		}
	} else {
		
		if(ISD_ADMIN_USER_LANG == ISD_DEFAULT_LANG) {
			return $str;
		} elseif(isset($added_array[$str])) {
			return $added_array[$str];
		} else {
			return $str;
		}	
	}
}
############################################################
# to samo co powyżej, tyle, ze wyświerla - DLA ADMINA
# 1 - tekst do tłumacenia
# 2 - tablica jezykowa, która ma być użyta zamiast domyślnej tablicy TRL
# 3 - język - jesli nie można go pobrać ze stałej ISD_ADMIN_USER_LANG - np. w aplikacjach AJAXowych
############################################################
function _t($str, $added_array = array(), $lang="") {
	global $TRL;	
	//ee($added_array);
	
	if(!is_array($added_array)) {
		echo $str;
		return;
	}
	if(isset($lang) && $lang!='') define("ISD_ADMIN_USER_LANG", $lang);
	if(!defined('ISD_ADMIN_USER_LANG')) {
		echo $str;
		return;
	}
	//ee($added_array);
	
	if(count($added_array)==0) {	
		if(ISD_ADMIN_USER_LANG == ISD_DEFAULT_LANG) {
			echo $str;
		} elseif(isset($TRL[$str])) {
			echo $TRL[$str];
		} else {
			echo $str;
		}
	} else {
		
		if(ISD_ADMIN_USER_LANG == ISD_DEFAULT_LANG) {
			echo $str;
		} elseif(isset($added_array[$str])) {
			echo $added_array[$str];
		} else {
			echo $str;
		}	
	}
}
############################################################
# zwraca odpowiednie tłumaczenie z tablicy TRL - DLA STRONY
# 1 - tekst do tłumacenia
# 2 - tablica jezykowa, która ma być użyta zamiast domyślnej tablicy TRL
# 3 - język - jesli nie można go pobrać ze stałej ISD_ADMIN_USER_LANG - np. w aplikacjach AJAXowych
############################################################
function _er($str, $added_array = array(), $lang="") {
	global $TRL;
	if(!is_array($added_array)) return;
	if(isset($lang) && $lang!='') define("LANG", $lang);
	
	if(!defined('LANG')) return $str;
	
	
	if(count($added_array)==0) {
		if(LANG == ISD_DEFAULT_LANG && LANG == ISD_BASE_LANG) {
			return $str;
		} elseif(isset($TRL[$str])) {
			return $TRL[$str];
		} else {
			return $str;
		}
	} else {
		
		if(LANG == ISD_DEFAULT_LANG && LANG == ISD_BASE_LANG) {
			return $str;
		} elseif(isset($added_array[$str])) {
			return $added_array[$str];
		} else {
			return $str;
		}	
	}
}
############################################################
# to samo co powyżej, tyle, ze wyświerla - DLA STRONY
# 1 - tekst do tłumacenia
# 2 - tablica jezykowa, która ma być użyta zamiast domyślnej tablicy TRL
# 3 - język - jesli nie można go pobrać ze stałej ISD_ADMIN_USER_LANG - np. w aplikacjach AJAXowych
############################################################
function _e($str, $added_array = array(), $lang="") {
	global $TRL;	
	if(!is_array($added_array)) {
		echo $str;
		return;
	}
	if(isset($lang) && $lang!='') define("LANG", $lang);
	if(!defined('LANG')) {
		echo $str;
		return;
	}
	if(count($added_array)==0) {
		if(LANG == ISD_DEFAULT_LANG && LANG == ISD_BASE_LANG) {
			echo $str;
		} elseif(isset($TRL[$str])) {
			echo $TRL[$str];
		} else {
			echo $str;
		}
	} else {
		
		if(LANG == ISD_DEFAULT_LANG && LANG == ISD_BASE_LANG) {
			echo $str;
		} elseif(isset($added_array[$str])) {
			echo $added_array[$str];
		} else {
			echo $str;
		}	
	}
}
#########################################################################
# metoda pochodząca  z Admin_Url_Site
function getPrefix($lang) {
	$prefix = '';
	if(ISD_LANG_MODE_SWITCH==1) {
		$prefix = '/'.$lang;
	} elseif(ISD_LANG_MODE_SWITCH == 2 && $lang != ISD_DEFAULT_LANG) {
		$prefix = '/'.$lang;
	} else {
		$prefix = '';
	}
	return $prefix;
}
function get_admin_langs() {
	global $ACONF;
	if(isset($ACONF['admin_langs']) && is_array($ACONF['admin_langs']) && count($ACONF['admin_langs'])>0) {
		return $ACONF['admin_langs'];												 	
	}
	return array();
}
function lang_is_ok($lang, $isd_langs_str) {
	if(!has_text($lang)) return false;
	if(!has_text($isd_langs_str)) return false;
	
	$arr = real_explode(";", $isd_langs_str);
	if(!array($arr) || empty($arr)) {
		return false;
	} else {
		if(in_array($lang, $arr)) {
			return true;	
		} else {
			return false;	
		}
		
	}
}
?>