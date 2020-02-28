<?php 
/*śćźżąłóć*/
/*include_once "pear/File/SWF.php";*/
function moveFile($name, $where_to) {
	$move = move_uploaded_file($_FILES[$name]["tmp_name"], $where_to);
	return $move;
}
	
function readDirectory($dir_adress, $type) {
	$arr = array();
	if ($handle = opendir($dir_adress)) {
		$i=0;
    		while (false !== ($file = readdir($handle))) { 
    			if(substr($file, -3)==$type && strtolower($type)!="swf") {
				
					$arr[$i]['name'] = $file;
					$arr[$i]['type'] = substr($file, -3);
					$arr[$i]['size'] = filesize($dir_adress.$file);
					$size = getimagesize($dir_adress.$file);
					$arr[$i]['width'] = $size[0];
					$arr[$i]['height'] = $size[1];
					$i++;
        		}
				
				# jesli to swf, inaczej czytamy wymiary 
				if(substr($file, -3)==$type && strtolower($type)=="swf") {
					$flash = new File_SWF($dir_adress.$file);
					
					$arr[$i]['name'] = $file;
					$arr[$i]['type'] = "swf";
					$arr[$i]['size'] = filesize($dir_adress.$file);
					if($flash->is_valid()){
						$size = $flash->getMovieSize();
						$arr[$i]['width']  =  $size[0];
						$arr[$i]['height'] =  $size[1];
					}
					
					$i++;
				}
				
    		}
    		closedir($handle); 
	}
	return $arr;
}
	
function readDirectoryNarrow($dir_adress, $type, $max_width, $max_height) {
	$arr = array();
	if ($handle = opendir($dir_adress)) {
		$i=0;
    		while (false !== ($file = readdir($handle))) { 
    			if(substr($file, -3)==$type) {
				$size = getimagesize($dir_adress.$file);
				if($size[0]<=$max_width && $size[1]<=$max_height ) {
					$arr[$i]['width'] = $size[0];
					$arr[$i]['height'] = $size[1];
					$arr[$i]['name'] = $file;
					$arr[$i]['type'] = substr($file, -3);
					$arr[$i]['size'] = filesize($dir_adress.$file);
					$i++;
				}
        			}
    		}
    		closedir($handle); 
	}
	return $arr;
}
function create_dirs_if_not_exist($names=array(), $attr) {
	foreach($names as $key=>$value) {
		if(!is_dir($value)) {
			mkdir($value, $attr);
		}
	}
}
function list_dir_files($dir_adress) {
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
function getThumbSizeByHeight($max_height, $init_width, $init_height) {
	$arr = array();
	if($max_height<$init_height) {
		$fac_1 = $init_height/$max_height;
		$arr[0] = round($init_width/$fac_1);
		$arr[1] = $max_height;
	} else {
		$arr[0] = $init_width;
		$arr[1] = $init_height;
	
	}
	return $arr;
}
function getThumbSizeByWidth($max_width, $init_width, $init_height) {
	$arr = array();
	if($max_width<$init_width) {
		$fac_1 = $init_width/$max_width;
	
		$arr[0] = $max_width;
		$arr[1] = round($init_height/$fac_1);
	} else {
		$arr[0] = $init_width;
		$arr[1] = $init_height;
	
	}
	return $arr;
}
function getThumbSize($max_side, $init_width, $init_height) {
	$arr = array();
	if($init_width > $max_side && $init_height > $max_side) {
		
		if($init_width>=$init_height) {
			$fac = $init_width/$max_side;
			$arr[0] = $max_side;
			$arr[1] = floor($init_height/$fac);
		} else {
			$fac = $init_height/$max_side;
			$arr[0] = floor($init_width/$fac);
			$arr[1] = $max_side;
		}
		
	} elseif ($init_width >= $max_side && $init_height < $max_side) {
		$fac = $init_width/$max_side;
		$arr[0] = $max_side;
		$arr[1] = floor($init_height/$fac);
	} elseif ($init_width < $max_side && $init_height >= $max_side) {
		$fac = $init_height/$max_side;
		$arr[0] = floor($init_width/$fac);
		$arr[1] = $max_side;
	} elseif ($init_width <= $max_side && $init_height <= $max_side) {
		$arr[0] = $init_width;
		$arr[1] = $init_height;
		
	} 
	
	return $arr;
}
function this_class_plugin_name($file) {
	$path = dirname($file);
	if(substr($path, -7) == 'classes') {
		$part = substr($path, 0, -7);
		$parts = real_explode('/', $part);
		$parts = reset_keys($parts);
		return $parts[(count($parts)-1)];
	} else {
		trigger_error('¬le zdefinionwana wtyczka');
		return false;
	}
}
#read file
function readFileData($file_path) {
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
function get_file_ext($filename) {
   	$pos = strrpos($filename, '.');
	if($pos===false) {
		return false;
	} else {
		return substr($filename, $pos+1);
	}
}
#update file
function updateFileData($file_path, $data) {
	if(empty($data)) {
		$data = ' ';
	}
	$handler = fopen($file_path, 'a');
	ftruncate($handler, 0); 
	if(get_magic_quotes_gpc()) {
		$data = stripslashes($data);
	} 
	$data = removeDoubleNewlines($data);
		
	fwrite($handler, $data);
	fclose($handler); 

}
#usuwa pliki z folderu, których nie ma podanej sciezce
# !!!! powinno być wywołane po zapisie!:)
function clearUnneeded($path, $files_that_stay) {
	if(substr($path, -1) != "/") $path = $path."/";
	$all_in_dir = list_dir_files($path);
	foreach($all_in_dir as $key=>$value) {
		if(is_file($path.$value) && !in_array($value, $files_that_stay)) {
			@unlink($path.$value);	
		}	
	}
}
# zwraca plik
function display($file_path) {
	ob_start();
	include $file_path;
	$out = ob_get_contents();
	ob_end_clean();
	return $out;
}
#
function copy_dir($src, $dst) {
    $dir = opendir($src);
    @mkdir($dst);
    while(false !== ( $file = readdir($dir)) ) {
        if(($file != '.') && ($file != '..')) {
			$file_path_to_copy = $src.'/'.$file;
			$file_path_dst = $dst.'/'.$file;
//			ee($file_path_to_copy);	
//            ee('<br>');
			if(is_dir($file_path_to_copy)) {
                copy_dir($file_path_to_copy, $file_path_dst);
            }
            else {
                copy($file_path_to_copy, $file_path_dst);
            }
        }
    }
    closedir($dir);
} 
function getActionId($script_name='') {
	if($script_name=='') {
		$_SESSION[HF.'_action_id'] = generateId();
		return $_SESSION[HF.'_action_id'] ;
	} else {
		$_SESSION[$script_name.'_action_id'] = generateId();
		return $_SESSION[$script_name.'_action_id'] ;	
	}
}
#alias dla getActionId, tyle, że wyświetla
function GID($script_name='') {
	if($script_name=='') {
		$_SESSION[HF.'_action_id'] = generateId();
		echo  $_SESSION[HF.'_action_id'] ;
	} else {
		$_SESSION[$script_name.'_action_id'] = generateId();
		echo $_SESSION[$script_name.'_action_id'] ;	
	}
}
function KEY_IS_SET() {
	return isset($_SESSION[HF.'_action_id']);
}
function AKEY() {
	return $_SESSION[HF.'_action_id'];	
}

function get_server_root($server_root) {
	if($server_root=='/') return '/';
	else return $server_root.'/';
}

?>