<?php
class Isd_Form {
	#alias dla getActionId, tyle, że wyświetla
	public static function gid($request_obj, $naming = NULL) {
		if($naming!=NULL) {
			$hf =  $naming;
		} else {
			$hf = self::get_hf($request_obj);
		}
		$_SESSION[$hf.'_action_id'] = self::generate_real_id();
		return $_SESSION[$hf.'_action_id'] ;	
	}
	public static function is_set($request_obj) {
		$hf = self::get_hf($request_obj);
		return isset($_SESSION[$hf.'_action_id']);
	}
	public static function key($request_obj) {
		$hf = self::get_hf($request_obj);
		return $_SESSION[$hf.'_action_id'];	
	}
	
	public static function key_custom($custom_naming) {
		return $_SESSION[$custom_naming.'_action_id'];	
	}
	
	public static function ok($request_obj, $key_name='gid', $custom_naming=NULL) {
		$gid = $request_obj->getParam($key_name, '');
		//ee( self::key($request_obj), 'wartość zmiennej zapisanej w sesji');
		//ee( $gid, 'wartość parametru GET ');
		if($custom_naming==NULL) {
			if(self::is_set($request_obj) && self::key($request_obj) == $gid ) return true;
		} else {
			if(self::is_set($request_obj) && self::key_custom($custom_naming) == $gid ) return true;	
		}
		return false;
	}
	
	public static function generate_real_id() {
		$str = microtime();
		return base64_encode ($str);
	}
	
	# zwraca aktualny wirtualny folder ze sciezki HTTP
	public static function get_hf($request_obj) {
		$module = $request_obj->getModuleName();
		$controller = $request_obj->getControllerName();
		$action = $request_obj->getActionName();
		$str = $module.'-'.$controller.'-'.$action;
		return $str;
	}
}
?>