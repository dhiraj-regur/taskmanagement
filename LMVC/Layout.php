<?php
class LMVC_Layout{

	private $_layouts;
	private static $instance;

	private function __construct(){
		$this->_layouts = array();
	}

	final public static function getInstance(){
		if (!isset(self::$instance)) {
			$c = __CLASS__;
			self::$instance = new $c;
		}
		return self::$instance;
	}

	public function setLayout($module_name, $layout_dir, $layout_file=null){
		$this->_layouts[$module_name] = array('layout_dir'=> $layout_dir,'layout_file'=>((empty($layout_file))?"Default.html":$layout_file));
	}

	public function getLayoutDir($module_name)
	{
		$layoutDir = "";
		if(array_key_exists($module_name, $this->_layouts))
		{
			$layoutDir .= "/". $this->removeEndSlashes($this->_layouts[$module_name]['layout_dir']);
		}
		else
		{
			if($module_name !="Default")
			{
				$layoutDir .= "/". $module_name;
			}
			$layoutDir .= "/layouts";
		}
		return $layoutDir;
	}

	public function getLayoutFile($module_name="")
	{
		$layout_file = "";
		if(array_key_exists($module_name, $this->_layouts))
		{
			$layout_file = (empty($this->_layouts[$module_name]['layout_file']))?'Default.html':$this->_layouts[$module_name]['layout_file'];
		}
		else
		{
			$layout_file = 'Default.html';
		}
		return $layout_file;
	}

	private function removeEndSlashes($_path){
		$path = $_path;
		if($path!=""){
			if(substr($path,0,1)=="/"){
				$path = substr($path,1,strlen($path));
			}
			if(substr($path,strlen($path)-1,1)=="/"){
				$path = substr($path,0,strlen($path)-1);
			}
		}
		return $path;
	}









}
?>