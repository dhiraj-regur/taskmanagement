<?php
	{
		$this->_smarty->template_dir = $_viewFragmentDir;
		$viewVars = $_viewFragmentVars;
		foreach($viewVars as $var=>$val){
			$this->_smarty->assign($var,$val);
		}
		return $this->_smarty->fetch($_viewFragmentFile);
	
	}	