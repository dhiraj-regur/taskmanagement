<?php
class Isd_Filer {
	public static function create_dirs_if_not_exist($names=array(), $attr=0777) {
		foreach($names as $key=>$value) {
			if(!is_dir($value)) {
				mkdir($value, $attr);
			}
		}
	}
	#usuwa pliki z folderu, których nie ma podanej sciezce
	# !!!! powinno byc wywolane po zapisie!:)
	public static function clearUnneeded($path, $files_that_stay) {
		if(substr($path, -1) != "/") $path = $path."/";
		$all_in_dir = self::list_dir_files($path);
		foreach($all_in_dir as $key=>$value) {
			if(is_file($path.$value) && !in_array($value, $files_that_stay)) {
				@unlink($path.$value);	
			}	
		}
	}
	
	public static function list_dir_files($dir_adress) {
		$arr = array();
		if(is_dir($dir_adress)) {
			if ($handle = opendir($dir_adress)) {
				$i=0;
				while (false !== ($file = readdir($handle))) { 
						if($file!="." && $file!="..") {
							$arr[] = $file;
						}
					}
					closedir($handle); 
			}
		}
		return $arr;
	}
	
	public static function read_file_data($file_path) {
		$arr = array("file_path"=>'', "file_data"=>'');
		if(is_file($file_path)) {
			if(filesize($file_path)>0) {
				$handler = fopen($file_path, 'r');
				$file_data = fread($handler, filesize($file_path));
				fclose($handler);
			} else {
				$file_data ="";	
			}
			$arr =  array("file_path"=>$file_path, "file_data"=>$file_data);
		}
		return $arr;
	}
	
	public static function real_scandir($path) {
		$files = scandir($path);
		$arr = array();
		foreach($files as $key=>$value) {
			if($value!="." && $value!="..") {
				$arr[] = $value;	
			}	
		}
		return $arr;
	}
	
	public static function remove_dir_contents($dir) {
		if(substr($dir, -1)!="/") {
			$dir .= "/";	
		}
		if(is_dir($dir)) {
			$files = self::real_scandir($dir);
			if(is_array($files)) {	
				foreach($files as $key=>$value) {
					unlink($dir.$value);	
				}
			}
		}
	}
}
?>