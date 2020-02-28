<?php
class Admin_UsersController extends LMVC_Controller{
	
	public function init(){		

		$this->setTitle('Users');           
		$this->setViewVar('userRoles',getUserRoles());
		
	}
	
	public function addAction()
	{
		$user = new Models_User();
		$this->setViewVar('company',$user);
		
		
		if($this->isPost())
		{
			$user->getPostData();
								
			$id = $user->create();
			
			if($id>0){							
				
				header('Location: /admin/users/index/id/'. $id ."/?success=user added");				
				exit();
				
			}
			else{
								
				if($user->hasErrors()){
					$this->setViewVar('error_list',$user->getErrors());
				}
				
			}
			
		}
		
		$this->setViewVar('user', $user);
		
		
	}

    public function indexAction()
    {
        $success_msg = $this->getRequest()->getVar('success');
        if ($success_msg == "user info changed") {
            $this->setViewVar('success_msg', 'User information updated successfully');
        } elseif ($success_msg == "user added") {
            $this->setViewVar('success_msg', 'A new user was added successfully');
        }
        $userRole = $this->getRequest()->getParam('userrole');
        if ($userRole == '')
            $userRole = '0';

        $this->setViewVar("userRole", $userRole);
    }

    public function listAction()
    {
        $this->setNoRenderer(true);
        $userRole = $this->getRequest()->getParam('userrole');
            
        $dt = new LMVC_DataTablesV2();
        $dt->setDBAdapter(LMVC_DB::getInstance()->getDB());
        $dt->setTable('admin_users');
        $dt->setIdColumn('id');
        $dt->addColumns(array(
            'userId' => 'id',
            'username' => 'username',
            'firstName' => 'firstName',
            'lastName' => 'lastName',
        	'email'=>'email',
            'active' => 'active',
            'role' => 'role'
        ));

     
        $defaultFilters=array();
        
       if($userRole!='' && $userRole!='0'){
           $defaultFilters['role'] = "'$userRole'";
          }
        $dt->setDefaultFilters($defaultFilters);
        $dt->registerRowProcessor('dgUserRoleLabel');        
        $dt->getData();
        
    

        die();
    }
	
	
	public function editAction()
	{
	    $this->registerHeaderScript('/assets/plugins/multi-select/multiselect.js');
	    $this->registerHeaderScript('/admin_resources/js/userrulessetupctrl.js');
	    $this->registerHeaderScript('/admin_resources/js/usercapsetupctrl.js');
	   
	    
		$userId = $this->getRequest()->getParam('id');		
		$user = new Models_User($userId);		
		if($user->isEmpty) {
		    $this->setViewVar('isValid', "false");
		} else {
    		if($this->isPost())
    		{
    			$password = $this->getRequest()->getPostVar("password");
    			
    			$ignoreFields = array();
    			if($password=="")
    			{
    				array_push($ignoreFields,"password");
    				array_push($ignoreFields,"confirmPassword");
    			}
    			
    			$user->getPostData($ignoreFields);
    			$result = $user->update();
    			if($result)			
    			{
    								
    				header("Location: /admin/users/?success=user info changed");
    				exit();
    			}
    			else
    			{
    				$this->addError($user->getErrors());
    			}			
    		}		
    		
    		
    		if($this->hasErrors())
    		{
    			$this->setViewVar('error_list',$this->getErrors());	
    		}
    		$this->setViewVar('user',$user);	
		}

	}
	
	

	
	public function deleteAction()
	{
		
		if($this->isPost())
		{
			$status = "";
			$mesage = "";
		
			$userId = $this->getRequest()->getPostVar('id');		
			$user = new Models_User($userId);
			if(!$user->isEmpty)
			{
				$user->delete();					
				$status = 1;
				$message = "User deleted successfully";
			}
			else
			{
				$status = 0;
				$message = "There was some error deleting the user";
					
			}
			
			$response = array('status'=>$status, 'message'=> $message);
			
			header('Content-type: application/json');
			echo json_encode($response);
		}
		die();
	}
	
  public function getrulesAction() {
      $this->setNoRenderer(true);
      
      $response = new LMVC_AjaxResponse('json');
      
      $leadStatuses = getAgentAssignableLeadStatuses();
      
      
      $userRulesLtcIds = implode(",",getUserRulesLtcIds());	
      
      $leadTypeCategory = new Models_LeadTypeCategory();
      $leadTypeCategories = $leadTypeCategory->getLeadTypeCategoriesName($userRulesLtcIds) ;
      
      
     $userId = $this->getRequest()->getParam('userId');
     $rules = array();
     $user = new Models_User($userId);	
     
     $rules = $user->getUnserializedUserData($user->userLeadAssignmentRules);
     
    $response->setData(array('rules'=>$rules,'leadStatuses'=>$leadStatuses,'leadTypeCategories'=>$leadTypeCategories));
              
      $response->output();
  }
	
  public function saverulesAction()
  {
   
      $this->setNoRenderer(true);
      $response = new LMVC_AjaxResponse('json');
      
      $userId = $this->getRequest()->getPostVar('id');	
      $userRules = $this->getRequest()->getPostVar('rules');		

    
      $user = new Models_User($userId);	

      $result = false;
      if($user->validateUserLeadAssignmentRules($userRules))
      {
          
          $user->userLeadAssignmentRules = $user->getSerializedUserData($userRules);
          
          $result = $user->update(array('userLeadAssignmentRules'));
    
          $message = "Rules has been updated successfully";
      }
      
      if ($result) {
          
          $response->setMessage($message);
          
      } else {
          
          $response->addError($user->getErrors());
      }
      
      $response->output();
      
  }
  
  public function getcapsettingsAction() {
        $this->setNoRenderer(true);

        $response = new LMVC_AjaxResponse('json');

        $conveyancingLeadTypeCategoryIds = implode(",", getUserRulesLtcIds());
        
        $leadTypeCategory = new Models_LeadTypeCategory();
        $leadTypeCategories = $leadTypeCategory->getLeadTypeCategoriesName($conveyancingLeadTypeCategoryIds) ;

        $userId = $this->getRequest()->getParam('userId');
        $userCapSettings = array();
        $caps = array();
        $caps['ltc'] = array();
        $user = new Models_User($userId);

        $userCapSettings = $user->getUnserializedUserData($user->userCapSettings);
        $rules =  $user->getUnserializedUserData($user->userLeadAssignmentRules);

        $rulesLtcIds = isset($rules['ltc']) ? array_keys($rules['ltc']) : array();

        // adding lead type categories which are only available in rules array
        if (! empty($rulesLtcIds) && isset($userCapSettings['ltc'])) {
            foreach ($userCapSettings['ltc'] as $ltcId => $value) {
                if (in_array($ltcId, $rulesLtcIds)) {
                    $caps['ltc'][$ltcId] = $value;
                }
            }
        }

        // adding lead type categories which are not available in caps and available in rules array.
        foreach ($rulesLtcIds as $key => $rulesLtcId) {
            if (! in_array($rulesLtcId, array_keys($caps['ltc']))) {
                $caps['ltc'][$rulesLtcId]['caps']['dailyCaps'] = '0';
            }
        }

        $response->setData(array(
            'caps' => $caps,
            'leadTypeCategories' => $leadTypeCategories
        ));

        $response->output();
    }
    
  public function savecapsettingsAction()
  {
        $this->setNoRenderer(true);
        $response = new LMVC_AjaxResponse('json');

        $userId = $this->getRequest()->getPostVar('id');
        $capSettings = $this->getRequest()->getPostVar('caps');

        $user = new Models_User($userId);

        $result = false;
        if($user->validateUserCapSettings($capSettings))
        {        
            $user->userCapSettings = $user->getSerializedUserData($capSettings);
        
                $result = $user->update(array(
                    'userCapSettings'
                ));
        
                $message = "Cap settings has been updated successfully";
           }

        if ($result) {

            $response->setMessage($message);
        } else {

            $response->addError($user->getErrors());
        }

        $response->output();
    }
	
    public function userscapsmanagementAction() 
    {
        $this->registerHeaderScript('/admin_resources/js/userscapsmanagementctrl.js');
    }
    
    public function getuserscapsmanagementdataAction() {
        
        $this->setNoRenderer(true);

        // getUserRulesLtcIds
        
        $ltcIds = implode(',',getUserRulesLtcIds());        
        $leadTypeCategory = new Models_LeadTypeCategory();
        $userRulesLtcIds = $leadTypeCategory->getLeadTypeCategoriesName($ltcIds);

        // get users data
        
        $user = new Models_User();
        $usersAllData = $user->getUsers();

        $usersData = array();
        foreach ($usersAllData as $index => $userAllData)
        {
            $userCapSettings = $user->getUnserializedUserData($userAllData['userCapSettings']);
            $userLeadAssignmentRules = $user->getUnserializedUserData($userAllData['userLeadAssignmentRules']);           
           
            if ( isset($userCapSettings['ltc']) && isset($userLeadAssignmentRules['ltc'])) {
                
                    foreach ($userCapSettings['ltc'] as $ltcId => $value) {         // removing ltcIds from $userCapSettings  which are not in $userLeadAssignmentRules
                        
                        if (!array_key_exists($ltcId, $userLeadAssignmentRules['ltc'])) {
                            unset( $userCapSettings['ltc'][$ltcId]);
                        }                       
                        
                    }
                    
                    foreach ($userLeadAssignmentRules['ltc'] as $ltcId => $value) {         // to add daily caps with value 0 in grid, for ltc which added in rules setup but not in caps setup
                        
                        if (!array_key_exists($ltcId, $userCapSettings['ltc'])) {
                            $userCapSettings['ltc'][$ltcId]['caps']['dailyCaps'] = 0;
                        }
                        
                    }
                    
                    
                    $usersData[$index]['id'] = $userAllData['id'];
                    $usersData[$index]['username'] = $userAllData['username'];
                    $usersData[$index]['name'] =  $userAllData['firstName'].' '.$userAllData['lastName'];
                    $usersData[$index]['role'] = getUserRoleValue($userAllData['role']);
                    $usersData[$index]['userCapSettings'] = $userCapSettings;

            }
        }

       // response

        $response = new LMVC_AjaxResponse('json');
        
        $response->setData(array('usersData'=>$usersData,'leadTypeCategories'=>$userRulesLtcIds));
        
        $response->output();
    }
    
    public function savealluserscapsettingsAction()
    {
        $this->setNoRenderer(true);

        $result = false;
        $message = '';
        $errors = [];

        $usersData = $this->getRequest()->getPostVar('usersData');
        $response = new LMVC_AjaxResponse('json');

        if (! empty($usersData) && is_array($usersData)) {
            foreach ($usersData as $userData) {
                if (isset($userData['id']) && is_numeric($userData['id'])) {

                    $user = new Models_User($userData['id']);

                    $user->validateUserCapSettings($userData['userCapSettings']);

                    if (! $user->hasErrors()) {

                        $userCapSettings = $user->getSerializedUserData($userData['userCapSettings']);

                        if ($userCapSettings != $user->userCapSettings) {

                            $user->userCapSettings = $userCapSettings;

                            $re = $user->update(array(
                                'userCapSettings'
                            ));

                            if ($re) {
                                $message = "Cap settings has been updated successfully";
                                $result = true;
                            } else {
                                $errors[] = "Error found while updating cap settings for userId: " . $userData['id'];
                                break;
                            }
                        }
                    }
                } else {
                    $errors[] = "User Id found empty";
                }
            }
        } else {
            $errors[] = "Users data not found";
        }

        if ($result) {

            $response->setMessage($message);
        } else {

            if ($user->hasErrors()) {
                $errors = array_merge($errors, $user->getErrors());
            }

            $response->addError($errors);
        }

        $response->output();
    }
	
}
?>