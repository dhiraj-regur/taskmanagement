<?php
	{
	
		$fullViewVars = $this->getViewVars();
		$viewVars = array_merge($_viewFragmentVars, array('view_vars'=> $fullViewVars));
		return $this->_viewRenderer->renderViewFragment($_viewFragmentDir, $_viewFragmentFile, $viewVars);
	}