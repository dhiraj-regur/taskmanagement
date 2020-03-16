<?php
require_once(dirname(__FILE__) ."/Mailer/class.phpmailer.php");

class Helpers_Mailer
{
	private static $mail;
	
	private static $instance;

	private function __construct(){}
	
	private function init(){
		
		global $siteConfig;
		
		self::$mail = new PHPMailer();
		
		self::$mail->IsMail();
		
		
		if((ENV ==  "development" || ENV == "staging") && defined('LOCAL_EMAILS_ON'))
		{
			self::$mail->IsSMTP();
			
			self::$mail->Host = "ssl://regur.in";
			
			self::$mail->SMTPAuth = true;
	
			self::$mail->SMTPDebug = false;
	
			self::$mail->Port = 465;
	
			self::$mail->Username =  $siteConfig->get('smtp_email');
	
			self::$mail->Password =  $siteConfig->get('smtp_password');
			
		}
						
	}
	
	public function clearAddresses()
	{
		self::$mail->ClearAddresses();
		self::$mail->ClearBCCs();
		self::$mail->ClearCCs();
		self::$mail->ClearReplyTos();
		self::$mail->ClearAllRecipients();
	}

	public static function getInstance()
	{
		if (!isset(self::$instance)) {
			$c = __CLASS__;
			self::$instance = new $c;
			self::$instance->init();			
		}		
		return self::$instance;	
	}
	
	public function getMailObject()
	{
		return self::$mail;
	}

    public function clearAllAttachments()
    {
        self::$mail->ClearAttachments();
    }
	
	public function addAttachment($filePath, $fileName, $encoding, $type, $clearPreviousAttachments = true)
	{
		if($clearPreviousAttachments) //clear any previous attachments
		{
			self::$mail->ClearAttachments();
		}
		self::$mail->AddAttachment($filePath,$fileName,$encoding,$type);
	}
	

	public static function sendMail($from,$to,$subject,$body,$extra_params=array(), $clearPreviousAddresses = true)
	{
		$toName = "";
		$bcc = "";
		$replyTo = "";
		$isHTML = true;

		extract($extra_params, EXTR_OVERWRITE);

		

		self::getInstance(); //generate mail object if not already generated
		
		
		//clear any previous addresses
		if($clearPreviousAddresses)
		{
			self::$instance->clearAddresses();
		}	

		$matches = array();
		

		//from email
		if(preg_match('/([^<]+)<([^>]+)>/',$from,$matches)>0)
		{
			self::$mail->From = trim($matches[1]);
			self::$mail->FromName = trim($matches[2]);
		}
		else
		{
			self::$mail->From = $from;
		}
		if(!empty($fromname)){
			self::$mail->FromName = $fromname;
		}
		
		
		//to email
       $emailList = $to;
        if(is_string($emailList) && strpos($emailList,',')!==false)
        {
            $to = explode(',',$emailList);
        }


		
		if(is_array($to))
		{
			foreach($to as $recipient)
			{
				$matches = array();
				if(preg_match('/([^<]+)<([^>]+)>/',trim($recipient),$matches)>0)
				{
					self::$mail->AddAddress(trim($matches[1]), trim($matches[2]));
				}
				else
				{
					self::$mail->AddAddress(trim($recipient),'');
				}
			}
		}
		else
		{
			if(preg_match('/([^<]+)<([^>]+)>/',$to,$matches)>0)
			{
				self::$mail->AddAddress(trim($matches[1]), trim($matches[2]));
			}
			else
			{
				self::$mail->AddAddress($to, $toName);
			}
		}
		
		
		//reply to email
		if(!empty($replyTo))
		{
			if(preg_match('/([^<]+)<([^>]+)>/',$replyTo,$matches)>0)
			{
				self::$mail->AddReplyTo(trim($matches[1]), trim($matches[2]));
			}
			else
			{
				self::$mail->AddReplyTo($replyTo);
			}
		}
		
		//bcc emails
		if(!empty($bcc))
		{
			self::$mail->AddBCC($bcc, "");
		}
		
		//debug emails (send as bcc).
		self::$mail->AddBCC(DEBUG_EMAIL, "Pinlocal");	
		//self::$mail->AddBCC('pinlocal.debug@gmail.com',"Pinlocal"); - don't keep it active!!!. turn off once your test is over as it queues lot of emails on the server.
		
		
		
		if(ENV ==  "development" || ENV == "staging")
		{
			$subject = "[STAGING] ". $subject;	//if its staging server we want all emails to have this identifier.
		}
	
		self::$mail->Subject = stripslashes($subject);
		
		
	
		
		
		if(true == $isHTML)
		{
			self::$mail->AltBody = "To view the message, please use an HTML compatible email viewer!\n\n Message: \n". stripslashes($body);
			self::$mail->MsgHTML(stripslashes($body));
		}
		else
		{
			// send a plain text email
					
			self::$mail->IsHTML(false);
			self::$mail->AltBody = "";
			self::$mail->Body =  stripslashes($body);
						
			
		}

        if(ENV ==  "development" || ENV == "staging")
        {
           self::$mail->ClearAddresses();  //we don't want to send emails to any real customers in dev mode

            self::$mail->AddAddress(DEBUG_EMAIL, "Pinlocal Diverted Emails");  //divert emails to debug emails
			//self::$mail->AddAddress('rob@pinlocal.com', "Pinlocal Diverted Emails");  //divert emails to debug emails

        }
		

		if(!self::$mail->Send()){
			$rtn = false;	//error.
		}
		else{
			$rtn = true;
		}


		if(ENV ==  "development" || ENV == "staging")
		{
			if( is_array($to) ) {
				$to = implode(",", $to);
			}
			
			$filename = APPLICATION_PATH ."/extras/logs/mail.txt";
			$somecontent = "\n=============================================\nSubject:". stripslashes($subject) ."\nFrom:$from\nTo:$to\nReplyTo:$replyTo\n\n===========================================\n". stripslashes($body);

			// Let's make sure the file exists and is writable first.
			if (is_writable($filename)) {

				// In our example we're opening $filename in append mode.
				// The file pointer is at the bottom of the file hence
				// that's where $somecontent will go when we fwrite() it.
				if (!$handle = fopen($filename, 'a')) {
					echo "Cannot open file ($filename)";
					exit;
				}

				// Write $somecontent to our opened file.
				if (fwrite($handle, $somecontent) === FALSE) {
					echo "Cannot write to file ($filename)";
					exit;
				}

				//echo "Success, wrote ($somecontent) to file ($filename)";

				fclose($handle);
				
				$rtn = true; //!! on staging and development we always return true.

			} else {
				trigger_error("Mail log is not writable", E_USER_NOTICE);
			}
			
			

		}

		return $rtn;

	}

}
?>