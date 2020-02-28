<?php
final class LMVC_PluginBroker{
	
	private $_plugins;	
	
	public function __construct(){
		$this->_plugins = array();	
	}
	
	public function registerPlugin($_plugin, $stack_index = null)
	{		
	 	if (false !== array_search($_plugin, $this->_plugins, true)) {
           trigger_error('Plugin is already registered', E_USER_ERROR);
           return;
        }        
        if(!empty($stack_index)){
        	if(isset($this->_plugins[$stack_index])){
        		trigger_error('There is a plugin already registered at index '. $stack_index, E_USER_ERROR);
        	}
        	else{
        		$this->_plugins[$stack_index] = $_plugin;
        	}
        }
        else{
        	array_push($this->_plugins, $_plugin);        	
        }		
	}
	
	public function preDispatch($request){
		foreach($this->_plugins as $_plugin)
		{
			if(method_exists($_plugin,'preDispatch')){
				$_plugin->preDispatch($request);	
			}
			else{
				$className = get_class($_plugin);
				trigger_error('preDispatch does not exist for plugin '.$className, E_USER_NOTICE);
			}			
		}
	}
	
	public function postDispatch(){
		foreach($this->_plugins as $_plugin)
		{
			if(method_exists($_plugin,'postDispatch')){
				$_plugin->preDispatch();	
			}
			else{
				$className = get_class($_plugin);
				trigger_error('postDispatch does not exist for plugin '.$className, E_USER_NOTICE);
			}			
		}
	}
}
?>