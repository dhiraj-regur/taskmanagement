<?php
abstract class LMVC_Session{	
	
	private function __contruct()
	{
		
	}
	
	public static function init()
	{
		session_start();
	}
	
	public static function destroy()
	{
		session_destroy();
	}
	
	public static function set($name,$value){
				
		$_SESSION[$name] = $value;	
	}
	
	public static function get($name){
		if(isset($_SESSION[$name])){
			return $_SESSION[$name];
		}	
	}
}
?>