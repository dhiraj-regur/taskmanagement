<?php

class Plugins_DefaultSessionValidator{

	

	private $exemptedProperties = array();

	

	public function preDispatch(LMVC_Request $request)

	{

		$this->exemptedProperties['login'] = array('index');

		$this->exemptedProperties['logout'] = array('index','success');

		$this->exemptedProperties['register'] = array('index','success','signupcomplete');

		
		if($request->getModuleName() == "Default")

		{

			$this->setVars();
			
			

			$currentController = $request->getControllerName();

			$currentAction = $request->getActionName();

			

			if(array_key_exists($currentController,$this->exemptedProperties))

			{

				

				if(in_array($currentAction,$this->exemptedProperties[$currentController]))

				{

					return;

				}

				else{

					

					$this->validateSession();

					

				}

				

			}

			else

			{

				$this->validateSession();

			}

			

			

		}



	}

	

	private function validateSession()

	{

		$id = LMVC_Session::get('userId');

		if(empty($id))

		{

			

			$this->setVars();

			$retUrl = $_SERVER['REQUEST_URI'];					

			header("Location: /login?retURL=$retUrl");

			exit();	

		}		

		

	}

	

	private function setVars()

	{

		$id = LMVC_Session::get('userId');		

		if(empty($id)) $id=0;

		LMVC_Front::getInstance()->setPreDispatchVar('session_user_id',$id);

	}

	

	

}

?>