<?php
class Models_User extends LMVC_ActiveRecord
{
	public $tableName = "admin_users";

	public $firstName 	= "";
	public $lastName 	= "";
	public $email 	= "";
	public $username 	= "";	
	public $password 	= "";
	public $confirmPassword = "";
	public $active		= "y";
	public $role            = "";
	public $userLeadAssignmentRules = "";
	public $userCapSettings  = "";
	private $oldPassword =  false;
	//#501
	public $userPhone  = "";
	public $emailAddressPrefix  = "";
	public $emailSignature  = "";
	
	
	public $dbIgnoreFields = array('id','confirmPassword','oldPassword');

	public function init()
	{
		
		if($this->id>0)
		{
			$this->confirmPassword = $this->password;
			$this->oldPassword = $this->password;
		}
					
		$this->addListener('beforeCreate', array($this,'doBeforeCreate'));
		
		$this->addListener('beforeUpdate', array($this,'doBeforeUpdate'));
						
		// $this->addListener('afterDelete', array($this,'doAfterDelete'));
	}



	protected function doBeforeCreate()
	{
		//create Login..
								
		if($this->validate())
		{
			if($this->password != "")
			{
				$this->password = md5($this->password);
			}
			return true;
		}
		else
		{
			return false;
		}
	}
	
	
	protected function doBeforeUpdate()
	{

		if($this->validate())
		{
			if($this->password != $this->oldPassword)
			{
				$this->password = md5($this->password);
			}			
			return true;
		}
		else
		{
			return false;
		}
	}

	public function validate()
	{

		$this->validateUserInfo();
		
		
		$this->validateLoginInfo();

		return !$this->hasErrors();
	}
	
	public function validateLoginInfo()
	{
		
		if($this->username=="")
		{
			$this->addError("Username is required");
		}
		else
		{
			if(!$this->isUniqueUsername($this->username,$this->id)){
				$this->addError("Username '". $this->username ."' is already in use");
			}
		}
		
		
		if($this->_getCurrentAction() == "create")
		{
			$this->validatePassword();
		}
		elseif($this->_getCurrentAction() == "update")
		{
			if($this->password!="")
			{			
				
				$this->validatePassword();
			}
		}
		
		if($this->role==""){
			$this->addError("User role is required");
		}
		
		return !$this->hasErrors();
	}
	
	private function validatePassword()
	{
		if($this->password=="")
		{
			$this->addError("Password is required");
		}
		else
		{
			if(strlen($this->password)<6)
			{
				$this->addError("Password must be minimum six characters long");
			}
			if($this->password != $this->confirmPassword)
			{
				$this->addError("Entered passwords do not match");
			}
		}
		
		
		return !$this->hasErrors();
	}
	
	public function validateUserInfo()
	{
		if($this->firstName=="")
		{
			$this->addError("First name is required");
		}
		
		
		if($this->lastName=="")
		{
			$this->addError("Last name is required");
		}

		if($this->email=="")
		{
		    $this->addError("Email is required");
		}

		if($this->userPhone=="")
		{
		    $this->addError("Phone is required");
		}
		
		if($this->emailAddressPrefix=="")
		{
		    $this->addError("Email Address Prefix is required");
		} else {
		    if(strpos($this->emailAddressPrefix, ' ') !== false || hasSpecialCharacters($this->emailAddressPrefix)) {
		        $this->addError("Please enter valid Email Address Prefix");
		    }
		}
		
		return !$this->hasErrors();
	}
	
	public function isUniqueUsername($_username,$id){

		$sql = "SELECT count(username) FROM ". $this->tableName ." WHERE username = '$_username'";

		if($id){
			$sql .= " AND id <> ". $id;
		}

		$count = $this->getDB()->getOne($sql);


		if($count==0){
			return true;
		}
		else{
			return false;
		}

	}
	
	public function checkUserHasAlreadyCheckedin()
	{
		
		$attendanceData = array();
		$isUserAlreadyCheckedin = false;
		$redisCheckFailed = false;
		$today = date('Y-m-d');
		$todayTimeStamp = strtotime($today);
		$checkedinTimeStamp = '';		
		$redisClientUser = new Service_AgentLeadAssignment_RedisClientUser($this->id);
		$attendanceData = $redisClientUser->getKeySpecificAgentLeadAssignmentData('userAttendance');
		if(!empty($attendanceData) && isset($attendanceData['attendanceDate']))
		{
			$checkedinTimeStamp = strtotime($attendanceData['attendanceDate']);
			if($todayTimeStamp == $checkedinTimeStamp)
			{
				$isUserAlreadyCheckedin = true;				
			}
			else 
			{
				$redisCheckFailed = true; 
			}	
		}
		else 
		{
			$redisCheckFailed = true;
		}	
		
		if($redisCheckFailed)
		{
			$userAttendance = $this->getUserAttendance();
			
			if(!$userAttendance->isEmpty)
			{
				$userAttendance->saveUserAttendanceToRedis();
				$isUserAlreadyCheckedin = true;
			}
		}	
		
		return $isUserAlreadyCheckedin;		
		
	}

	public function validateUserLeadAssignmentRules($data) 
    {
        if(isset($data['ltc']))
        {
            foreach ($data['ltc'] as $ltcId => $partners)
            {
                if(!empty($ltcId) && is_numeric($ltcId))
                {
                    if(isset($partners['partners']))
                    {
                        foreach ($partners['partners'] as $key => $partnerId)
                        {
                        	if(empty($partnerId))
                            {
                                $this->addError('Partner Id not found');
                            }
                        }
                        
                    }
                    else {
                        $this->addError('Data not in format');
                    }
             }
             else {
                 $this->addError('Lead type category should be numeric');
             }
                
            }
            
        }
        /* else {
            $this->addError('lead type category not found');
        } */
        
        return !$this->hasErrors();
        
    }
    public function validateUserCapSettings($data)
    {
        
        if(isset($data['ltc']))
        {
            foreach ($data['ltc'] as $ltcId => $cap)
            {
                if(!empty($ltcId) && is_numeric($ltcId))
                {
                
                    if(!isset($cap['caps']) || !isset($cap['caps']['dailyCaps']) || !is_numeric($cap['caps']['dailyCaps']))
                    {
                        $this->addError('Data not in format');
                    }
            
                }else {
                    $this->addError('Lead type category should be numeric');
                }
                
            }
            
        }
       /*  else {
            $this->addError('lead type category not found');
        } */


        return !$this->hasErrors();
        
    }    

    public function getUnserializedUserData($userData)
    {
        return  unserialize($userData);
    }
    
    public function getSerializedUserData($userData)
    {
        return serialize($userData);
    }
    
    public function getUsers($filters=array()) {
        
        
        $where = '';
        if(isset($filters['status'])){
        	if($filters['status'] == 'active')
        		$where .= "AND active = 'y' ";
        	else if($filters['status'] == 'inactive')
        		$where .= "AND active = 'n' ";	
        }
        
        if(isset($filters['roles']) && !empty($filters['roles']))
        	$where .= "AND role IN (" . $filters['roles'] . ")";
        
        
        $sql = "SELECT id,username,firstName,lastName,active,role,userLeadAssignmentRules,userCapSettings  FROM  admin_users";
        if($where != "") {
            $sql .=" WHERE 1 ". $where;
        }
        
        $sql .= " ORDER BY firstName ASC";

        return $this->findAll($sql,DB_FETCHMODE_ASSOC);
        
    }

    
    public function getUserAttendance()
    {
    	$userAttendance = new Models_AdminUserAttendance();
    	$userAttendance->fetchByProperty(array('userId','attendanceDate'),array($this->id,date('Y-m-d')));    	
    	return $userAttendance;
    }    

    /**
     * #501 - add agent specific information in Altium and CI emails
     * If set, use partner domain extension or use agent default email address
     * @param string $domainExtension
     * @return string
     */
    public function getPartnerAgentEmail($domainExtension) {
         $partnerAgentEmail = '';
         if(!empty($domainExtension)) {
             if(!empty($this->emailAddressPrefix)) {
                $partnerAgentEmail = $this->emailAddressPrefix.'@'.$domainExtension;
             }
         }
         if(empty($partnerAgentEmail)) {
            $partnerAgentEmail = $this->email;
         }
         return $partnerAgentEmail;
     }

     /**
      * #501 - add agent specific information in Altium and CI emails
      * Replace agent place holders in email body.
      * @param string $body
      * @param string $agentCompanyName
      * @return string
      */
     public function parseAgentPlaceholders($body, $agentCompanyName, $agentEmail, $tpLogo) {
         $placeholders =  array();
         $agentName = $this->firstName;
         if(!empty($this->lastName)) $agentName .= " ".$this->lastName;
         $placeholders['AGENT_NAME']			 = $agentName;
         $placeholders['AGENT_EMAIL']		     = $agentEmail;
         $placeholders['AGENT_PHONE']		     = $this->userPhone;
         $placeholders['AGENT_COMPANY_NAME']     = $agentCompanyName;
         //$placeholders['TP_LOGO']                = $tpLogo;

         $agentSignature = $this->emailSignature;
         //If lead didn't assigned to any agent use default signature.
         if(empty($agentName)) {
             $agentSignature = '
             Regards
             Team {AGENT_COMPANY_NAME}';
         }
         //If agent not configured signature use default signature.
         elseif(empty($agentSignature)) {
            $agentSignature = '
            Regards

            {AGENT_NAME}
            {AGENT_EMAIL}
            {AGENT_PHONE}';
         }
         $emailBody = $body;

         if(!empty($placeholders)) {
             foreach($placeholders as $key=>$val)
             {
                 $agentSignature = str_replace('{'. $key .'}', $val, $agentSignature);
                 $emailBody = str_replace('{'. $key .'}', $val, $emailBody);
             }
         }

         $agentSignature = nl2br($agentSignature);
         $emailBody = str_replace('{AGENT_SIGNATURE}', $agentSignature, $emailBody);
         return $emailBody;
     }
}
?>