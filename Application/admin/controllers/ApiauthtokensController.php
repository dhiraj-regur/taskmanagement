<?php
class Admin_ApiauthtokensController extends LMVC_Controller
{

	public function init()
	{
		$this->setTitle('API Auth Tokens');

	}
	
	private function loadCommonViewVars() {
		
		$this->setViewVar('testModes', array('y' => 'Yes', 'n' => 'No'));
		$this->setViewVar('activeOptions', array('y' => 'Yes', 'n' => 'No'));
	}
	
	
	public function addAction()
	{
		
		$this->loadCommonViewVars();
		
		$apiAuthToken = new Models_ApiAuthToken();

		if($this->isPost())
		{
			$apiAuthToken->getPostData();
						
			//Generating API Auth Token
			$apiAuthToken->token = md5($apiAuthToken->name . time());

			
			$id = $apiAuthToken->create();
				
			if($id>0){
	
				header('Location: /admin/apiauthtokens/index/id/'. $id ."/?success=auth_token_added");
				exit();
	
			}
			else{
	
				if($apiAuthToken->hasErrors()){
					$this->setViewVar('error_list',$apiAuthToken->getErrors());
				}
	
			}
				
		}
	
		$this->setViewVar('token',$apiAuthToken);
	
	
	}
	
	public function indexAction()
	{
	
		$success_msg = $this->getRequest()->getVar('success');
		if($success_msg == "auth_token_updated")
		{
			$this->setViewVar('success_msg','Auth token updated successfully');
		}
		elseif($success_msg == "auth_token_added")
		{
			$this->setViewVar('success_msg','Auth token was added successfully');
		}
	
	}
	
	
	public function listAction()
	{
		$dt = new LMVC_DataTablesV2();
		$dt->setDBAdapter(LMVC_DB::getInstance()->getDB());
		$dt->setTable('api_auth_tokens');
		$dt->setIdColumn('api_auth_tokens.id');
		$dt->addColumns(array('id'=>'id',
							 'name'=>'name',
							 'token'=>'token',
							 'active'=>'active',
				             'testMode'=>'testMode'));
		
		$defaultFilters['deleted'] = "'n'";
		$defaultFilters['partnerId'] = "0";
		
		if(!empty($defaultFilters)){
			
			$dt->setDefaultFilters($defaultFilters);
			
		}
		$dt->getData();
		die();
	}
	
	
	public function editAction()
	{
		
		$this->loadCommonViewVars();
	
		$tokenId = $this->getRequest()->getParam('id','numeric',0);
		$apiAuthToken = new Models_ApiAuthToken($tokenId);
		
		if(!$apiAuthToken->isEmpty && $apiAuthToken->partnerId == 0) {
			
			if ($this->isPost()) {
			
				$apiAuthToken->getPostData();
			
				$result = $apiAuthToken->update();
				if ($result) {
			
					header("Location: /admin/apiauthtokens/?success=auth_token_updated");
					exit();
				} else {
					$this->addError($apiAuthToken->getErrors());
				}
			}
			
			
			$this->setViewVar('token',$apiAuthToken);
			
			
		} else {
			
			// Invalid API Auth Token Id
			$this->addError("Invalid API Auth Token Id");
			
			
		}
		

        if ($this->hasErrors()) {
            $this->setViewVar('error_list', $this->getErrors());
        }
        
		
	}
	
	
	
	
	public function deleteAction()
	{
	
		if($this->isPost())
		{
			$status = "";
			$mesage = "";
	
			$tokenId = $this->getRequest()->getPostVar('id');
			$apiAuthToken = new Models_ApiAuthToken($tokenId);
			if(!$apiAuthToken->isEmpty)
			{
				//$user->delete();
				
				$apiAuthToken->deleted = 'y';
				
				$id = $apiAuthToken->update(array('deleted'));
				
				if($id>0){
					$status = 1;
					$message = "Auth token deleted successfully";
				}else{
					$status = 0;
					$message = "There was some error deleting the auth token";
				}
				
				
			}
			else
			{
				$status = 0;
				$message = "There was some error deleting the auth token";
					
			}
				
			$response = array('status'=>$status, 'message'=> $message);
				
			header('Content-type: application/json');
			echo json_encode($response);
		}
		die();
	}
	
}