<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Penuel
 * Date: 30/5/13
 * Time: 1:29 PM
 * To change this template use File | Settings | File Templates.
 */

abstract class Helpers_Mailer_Abstract
{
    protected $recipients = array();
    private $fromEmail = "";
    private $fromName = "";
    private $ccEmails = array();
    private $bccEmails = array();
    private $replyToEmail = "";
    private $subject  = "";
    private $htmlBody = "";
    private $textBody = "";
    private $attachments = array();
    private $errors = array();


    final public function __construct()
    {
        $this->init();
    }

    public function init()
    {
        unset($this->recipients);
        $this->recipients = array();
        $this->fromEmail = "";
        $this->fromName = "";
        unset($this->ccEmails);
        $this->ccEmails = array();
        unset($this->bccEmails);
        $this->bccEmails = array();
        $this->subject  = "";
        $this->htmlBody = "";
        $this->textBody = "";
        $this->attachments = array();
        $this->errors = array();

        //add default BCC
        $this->addBCC(DEBUG_EMAIL);
        //$this->addBCC('penuelr@gmail.com');
    }

    public function addAttachment($filePath, $fileType =""){
        array_push($this->attachments, array($filePath, $fileType));
    }

    public function  getAttachments()
    {
        return $this->attachments;
    }


    public function clearAttachments()
    {
        unset($this->attachments);
        $this->attachments = array();
    }
    
	public function clearErrors() {
		unset ( $this->errors );
		$this->errors = array ();
	}
    
    public function addRecipient($email,$name)
    {

        if(empty($email)){
            return false;
        }
        
        // Override `to` email address with the Debug email on the staging and dev environments.
    	// Allow to send emails on real email address if `FORCE_REAL_NOTIFICATIONS` constant is defined
    	
        if((ENV ==  "development" || ENV == "staging") && defined('FORCE_REAL_NOTIFICATIONS') === FALSE)
        {
            $this->clearRecipients();
            array_push($this->recipients, array('email'=>DEBUG_EMAIL,'name'=>'Pinlocal Diverted Emails'));
            if(defined('DEBUG_EMAIL2'))
            {
                array_push($this->recipients, array('email'=>DEBUG_EMAIL2,'name'=>'Pinlocal Diverted Emails"'));
            }
        }
        else
        {
            array_push($this->recipients, array('email'=>$email,'name'=>$name));
        }

    }

    final public function clearRecipients()
    {
      unset($this->recipients);
      $this->recipients = array();
    }

    public function addBcc($email)
    {
        if(!empty($email))
        {
          array_push($this->bccEmails, $email);
        }
    }
    
    public function addCC($email,$name='')
    {
        if(!empty($email))
        {
            array_push($this->recipients, array('email'=>$email,'name'=>$name,'type'=>'cc'));
        }
    	
    }

    public function setSender($email,$name)
    {
       $this->fromEmail = $email;
        $this->fromName = $name;
    }
    
    public function setReplyTo($email)
    {
    	$this->replyToEmail = $email;
    }
    
    public function getReplyTo()
    {
    	if($this->replyToEmail != '')
    		return $this->replyToEmail;
    	else
    		return $this->fromEmail;
    }

    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    public function setBody($htmlBody, $textBody="")
    {
        $this->htmlBody = $htmlBody;
        $this->textBody = $textBody;
    }

    final public function getRecipients()
    {
        return $this->recipients;
    }

    final public function getBccEmails()
    {
        return $this->bccEmails;
    }

    final public function getSender()
    {
        return array('email'=>$this->fromEmail,'name'=>$this->fromName);
    }

    final public function getFromEmail()
    {
        return $this->fromEmail;
    }

    final public function getFromName()
    {
        return $this->fromName;
    }


    final public function getSubject()
    {
        return $this->subject;
    }

    final public function getHtmlBody()
    {
        return $this->htmlBody;
    }

    final public function getTextBody()
    {
        return $this->textBody;
    }

    final public function addError($error)
    {
        array_push($this->errors, $error);
    }

    final public function getErrors()
    {
        return $this->errors;
    }

    final public function hasErrors()
    {
        return (empty($this->errors))?false:true;
    }

    final public function sendMail($from,$to,$subject,$body,$extra_params =array())
    {
    	
    	$this->clearErrors(); //clear any previously registered errors if any..
    	
        $fromName = "";
        $toName = "";
        $bcc = "";
        $replyTo = "";
        $isHTML = true;
        $clearOldRecipients = false;

        extract($extra_params, EXTR_OVERWRITE);

        //from
        if(preg_match('/([^<]+)<([^>]+)>/',$from,$matches)>0)
        {
            $this->setSender($matches[1],$matches[2]);
        }
        else
        {
            $this->setSender($from,$fromName);

        }

        //to
        if($clearOldRecipients === true)
        {
            $this->clearRecipients();
        }

        $toEmails = array();
        if(strpos($to,","))
        {
            $toEmails = explode(",", $to);
        }
        else
        {
            
            $toEmails[] = $to;
        }

        foreach($toEmails as $toEmail)
        {
            $matches = array();

            if(preg_match('/([^<]+)<([^>]+)>/',$toEmail,$matches)>0)
            {
                $this->addRecipient(trim($matches[1]), trim($matches[2]));
            }
            else
            {
                $this->addRecipient($toEmail, $toName);
            }
        }

        



        //bcc
        if(!empty($bcc))
        {
            $this->addBCC($bcc);
        }


        //subject (Update subject line on the staging and dev environments)
        //subject remains the same if `FORCE_REAL_NOTIFICATIONS` constant is defined
        
        if( (ENV ==  "development" || ENV == "staging") && defined('FORCE_REAL_NOTIFICATIONS') === FALSE)
        {
            $subject = "[STAGING] ". $subject;	//if its staging server we want all emails to have this identifier.
        }
        $this->setSubject(stripslashes($subject));

        //body
        if(true == $isHTML)
        {
            $this->setBody(stripslashes($body),"To view the message, please use an HTML compatible email viewer! Message:". stripslashes($body));
        }
        else
        {
            $this->setBody('',stripslashes($body));
        }

        $rtn = $this->send();

        return $rtn;
    }

    abstract public function send();
}