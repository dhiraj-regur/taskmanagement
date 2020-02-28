<?php
class Helpers_PasswordStrengthChecker{
	
	private $minimumLength;
	private $minimumDigits;
	private $minimumChars;	
	private $lastError;
	
	
	
	public function validatePassword($password){
		
		
		//must contain spaces.
		
		$spaces = explode(' ', $password);
		

		if(count($spaces)>1){
			$this->lastError = "Your password must not contain any spaces";
			return false;
		}
		
		//minimum length check;
		if(strlen($password)<$this->minimumLength){
			$this->lastError = "Your password must be at least {$this->minimumLength} characters long";
			return false;
		}
		
		//minimum digits check;
		$matches=array();		
		preg_match_all('/[0-9]/',$password,$matches);		
		if(count($matches[0])<$this->minimumDigits){
			$this->lastError = "Your password must contain at least {$this->minimumDigits} digits";
			return false;
		}
		
		//minimum chars check;
		$matches=array();
		preg_match_all('/[a-zA-Z]/',$password,$matches);
		if(count($matches[0])<$this->minimumChars){
			$this->lastError = "Your password must contain at least {$this->minimumChars} characters";
			return false;
		}
		
		
		return true;
	
	}
	
}
?>