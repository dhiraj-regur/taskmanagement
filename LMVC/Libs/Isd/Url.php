<?php
class Isd_Url {
    public static function get_safe_param($param='') {
		$param = preg_replace( '/[^a-zżźćńłśąóęŻŹĆŃŁŚĄÓĘ0-9 -_]/i', '', $param );
		return $param;
	}
	
	public static function get_uri_without_get($uri) {
		$uri_parts = explode('?', $_SERVER['REQUEST_URI'], 2);
		return @ $uri_parts[0];
	}
}
?>