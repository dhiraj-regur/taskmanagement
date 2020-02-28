<?php
/*śćźżąęółć*/
include_once 'Zend/File/Transfer/Adapter/Http.php';
include_once 'Abs.php';
class Isd_FileUploader extends Isd_Abs {
	var $adapter;
	var $destination_folder;
	var $client_destination_folder;
	var $form_file_name;
	var $infobox;
	var $file_full_path;
	var $is_ok = true;
	var $image_validator_enabled =  true;
	var $max_size = 1000000;
	/*typy nazw
	1 - podana jako argument 
	2 - podana jako argument plus wskaznik czasu
	3 - domy¶lne: sam wska¼nik czasu
	*/
	
	function __construct($destination_folder_1, $destination_folder_2) {
		parent::__construct();
		$destination_folder = $this->_getPath($destination_folder_1,$destination_folder_2);
		
		$this->adapter = new Zend_File_Transfer_Adapter_Http();
		
		if(substr($destination_folder, -1) != "/") {
			$this->destination_folder = $destination_folder."/";
			$this->client_destination_folder = $destination_folder_2."/";
		} else {
			$this->destination_folder = $destination_folder;
			$this->client_destination_folder = $destination_folder_2;
		}
		
		$this->adapter->setDestination($this->destination_folder);
	}
	
	public function disableImageValidator() {
		$this->image_validator_enabled = false;	
	}
	
	#upload obrazka
	public function uploadImage($form_file_name, $types=array()) {
		$this->form_file_name = $form_file_name; 
		
		/* validatory */
		if($this->image_validator_enabled) {
			$this->adapter->addValidator('IsImage', false);
		}
		
		$this->adapter->addValidator('Size', false, $this->max_size);
		
		if(is_array($types) && count($types)>0) {
			$this->adapter->addValidator('MimeType', false, $types);	
		}
		
		$temp = $this->adapter->getFileInfo();
		
		$this->infobox = $temp[$this->form_file_name];
		$this->adapter->receive();
		
		if (!$this->adapter->receive()) {
			$mess = $this->adapter->getMessages();
			if(!empty($mess)) {
				$this->is_ok = false;
				$this->addErr(implode(',', $mess));
				foreach($mess as $key=>$value) {
					switch($key) {
						case "fileIsImageFalseType":
							$this->addErr("plik nie jest obrazkiem");
						break;
						case "fileMimeTypeFalse":
							$this->addErr("plik jest niedozwolonego typu");
						break;	
						
						case "fileUploadErrorFormSize":
							$this->addErr("plik jest zbyt duzy, maksymalna wielkosc to ".$this->sizeFormat($max_size));
						break;	
					}
				} 
			}
		}
		
		if($this->no_err()) {
			$this->infobox['is_image'] = true;
		} else {
			$this->infobox['is_image'] = false;	
			$this->infobox['errors'] = $this->errors();	
		}
		
		//ee($this->infobox);
	}
	
	#przemianowanie ostaniego uplaodowanego pliku - domy¶lnie: znacznik czasu
	public function renameLastImage($name_type="", $new_name="") {
		if(empty($this->ans['errors'])) {
			$datetime = date("ymdHis");
			
			/*typ nazwy pliku*/
			$this->setAllFileInfo();
			if($name_type==1 && trim($new_name)!="") {
				$this->infobox['new_bare_name'] = $new_name;
			} elseif($name_type==2) {
				$this->infobox['new_bare_name']  = $new_name."_".$datetime;
			} else {
				$this->infobox['new_bare_name'] = $datetime;
			} 
					
			$this->infobox['new_name'] = $this->infobox['new_bare_name'].".".$this->infobox['extension'];
			$this->infobox['full_path'] = $this->destination_folder.$this->infobox['new_name'];
			$this->infobox['client_path'] = "/".$this->client_destination_folder.$this->infobox['new_name'];
			$n = rename($this->destination_folder.$this->infobox['name'], $this->infobox['full_path']);
			
			return $this->infobox;
		}
	}
	
	private function sizeFormat($size){
		if($size>0) {
			$filesizename = array(" Bytes", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB");
			return $size ? round($size/pow(1024, ($i = floor(log($size, 1024)))), 2) . $filesizename[$i] : '0 Bytes';
		} else {
			return -1;
		 }
	}
	
	private function get_file_extension($filename) {
		return end(explode(".", $filename));
	}
	
	private function setAllFileInfo() {
		$pathinfo = pathinfo($this->destination_folder.$this->infobox['name']);
		$this->infobox['bare_name'] =  $pathinfo['filename'];
		$this->infobox['extension'] =  $pathinfo['extension'];
		$this->infobox['realpath'] = realpath($this->destination_folder.$this->infobox['name']);
		
		if($this->infobox['is_image']==true) {
			$info = getimagesize($this->destination_folder.$this->infobox['name']);	
			$this->infobox['width'] = $info[0];
			$this->infobox['height'] = $info[1];
		}
	
	}	
	
	public function getThumbSize($max_side) {
		$file_data = $this->infobox;
		$init_width = $file_data['width'];
		$init_height = $file_data['height']; 
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
	
	public function getThumbSizeByHeight($max_height) {
		$arr = array();
		$file_data = $this->infobox;
		$init_width = $file_data['width'];
		$init_height = $file_data['height'];
		
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
	
	public function getThumbSizeByWidth($max_width) {
		$arr = array();
		$file_data = $this->infobox;
		$init_width = $file_data['width'];
		$init_height = $file_data['height'];
	
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
	
	private function _getPath($destination_folder_1,$destination_folder_2) {	
		if(substr($destination_folder_1, -1) != "/") {
			$destination_folder_1 .= '/';	
		}
		
		if(substr($destination_folder_2, 0, 1) == "/") {
			$destination_folder_2 = substr($destination_folder_2);	
		}
		return $destination_folder_1.$destination_folder_2;
	}
	
}
?>