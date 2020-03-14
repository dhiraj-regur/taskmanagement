<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Penuel
 * Date: 30/5/13
 * Time: 1:33 PM
 * To change this template use File | Settings | File Templates.
 */

class Helpers_Mailer_Mandrill extends Helpers_Mailer_Abstract
{

    private $apiKey = '';
    private $tags = '';
    private $metadata = '';
    private $requestData = '';
    
    private $preserveRecipients = "false";
    
    private $responseStatus = '';
    private $responseRejectReason = '';
    
    public function addBcc($email)
    {    	
        if(!empty($email))
        {
    	   array_push($this->recipients, array('email'=>$email,'type'=>'bcc'));
        }
    }
    
     public function addBCCMulti($emails = array())
     {
         foreach($emails as $email)
         {
            $this->addBCC($email);
         }
     } 
    
    public function addCC($email,$name='')
    {
        if(!empty($email))
        {
    	   array_push($this->recipients, array('email'=>$email,'name'=>$name,'type'=>'cc'));
        }
    }

    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function setTags($tags)
    {
        $this->tags = $tags;
    }

    public function setMetaData($metadata)
    {
        if(!is_array($metadata)) trigger_error('meta data must be of type array with key and value pairs', E_USER_ERROR);
        else     $this->metadata = $metadata;
    }

    public function init()
    {
        parent::init();
    }

    public function getRequestData()
    {
        return $this->requestData;
    }
    
    public function setPreserveRecipients($value){
    	
    	$this->preserveRecipients = $value;
    }
    
    public function getResponseStatus(){
        
        return $this->responseStatus;
    }
    
    public function getResponseRejectReason(){
        
        return $this->responseRejectReason;
    }
    
    public function isSendable(){
        
        $isSendable = true;
        
        if($this->responseStatus == "rejected" || $this->responseStatus == "invalid"){
            
            $isSendable = false;
        }
        
        return $isSendable;
    }

    public function send()
    {
    	
    	$this->clearErrors(); //clear any previously registered errors before sending..
    	
        //echo "Sending..";

        $toEmails = json_encode($this->getRecipients());
        $bccEmails = "";
        $tmp  = $this->getBccEmails();

        if(count($tmp)>0) $bccEmails = $tmp[0];


        $attachments = $this->getAttachments();

        $mandrill_attachments = array();
        $mandirll_attachments_json = '';

        foreach($attachments as $attachment)
        {
            $file = $attachment[0];

            if(!empty($file) && file_exists($file))
            {
                $handle = fopen($file, "rb");
                $content = fread($handle, filesize($file));
                fclose($handle);

                if($content!==FALSE)
                {
                    $type = '';
                    if(function_exists('finfo_open'))
                    {
                        $finfo = finfo_open(FILEINFO_MIME_TYPE);
                        $type = finfo_file($finfo, $file);
                    }
                    else{
                        $type = mime_content_type($file);
                    }

                    $base64data=base64_encode($content);
                    array_push($mandrill_attachments, array(
                        'content' => $base64data,
                        'name' => basename($file),
                        'type' => $type
                    ));
                }
            }
        }

        if(!empty($mandrill_attachments))
        {
            $mandirll_attachments_json = json_encode($mandrill_attachments);
        }

        $htmlBody = preg_replace('/\t/', '', $this->getHtmlBody());
        $htmlBody = stripslashes($htmlBody);
        $htmlBody = preg_replace('/\n/', '', preg_replace('/\r\n/', '' , str_replace('"', '\"', $htmlBody)));
		
		
		
        $plainText = preg_replace('/\t/', '', $this->getTextBody());
        $plainText = stripslashes($plainText);
        $plainText = preg_replace('/\n/', '', preg_replace('/\r\n/', '' , str_replace('"', '\"',  $plainText)));
		

        $data = '{
            "key":"'. $this->apiKey .'",
            "message":{
                "html": "'.$htmlBody.'",
                "text": "'.$plainText.'",
                "subject": "'. $this->getSubject() .'",
                "from_email": "'. $this->getFromEmail() .'",
                "from_name": "'. $this->getFromName() .'",
                "to": '. $toEmails .',
                "headers":
                {
                    "Reply-To": "'. $this->getReplyTo() .'"
                },' ."\n";

                if(!empty($mandirll_attachments_json))
                {
                    $data .= '"attachments":'. $mandirll_attachments_json .',' ."\n";
                }
                if(!empty($this->tags))
                {
                    $data .= '"tags":'. json_encode(explode(",",$this->tags)) .','. "\n";
                }
                if(!empty($this->metadata))
                {
                    $data .= '"metadata":'. json_encode($this->metadata) .','. "\n";
                }
                $data .= '"important": false,
                "track_opens": true,
                "track_clicks": true,
                "auto_text": null,
                "auto_html": null,
                "inline_css": null,
                "url_strip_qs": null,
                "preserve_recipients": '.$this->preserveRecipients.',
                "bcc_address": "'. $bccEmails .'",
                "merge": true,
                "tracking_domain": null,
                "signing_domain": "pinlocal.com"
            },
            "async": false
        }';

				
        $this->requestData = $data;

        //API endpoint to check downtime response
        //$ch = curl_init('https://mandrillapp.com/api/1.0/users/test503.json');
        $ch = curl_init('https://mandrillapp.com/api/1.0/messages/send.json');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $output = curl_exec($ch);
        
        /* curl_error function called before closing curl session
        If we call after closing curl session it throws notice */
        $curlError = '';
        if(curl_errno($ch)){
            $curlError = curl_error($ch);  
        }
        
        curl_close($ch);

        if($output ===  FALSE)
        {
            $this->addError("Error connecting to Mandrill API ". $curlError);
        }
        else 
        {
        	$arr_output = json_decode($output);
        	
        	$redis = getRedisInstance();
        	$saveEmailSentLog = "true";
        	if(!empty($redis)) {
        	    $redisFailedEmailKey = 'saveEmailSentLog';
        	    $saveEmailSentLog = $redis->get($redisFailedEmailKey);
        	}
        	if($saveEmailSentLog=="true") {
        	    $mailSentResponse = "Date: ". date('d/m/Y H:i:s')."\n";
        	    $mailSentResponse .= "subject: ". $this->getSubject()."\n";
        	    $mailSentResponse .= "To : ". $toEmails."\n";
        	    $mailSentResponse .= "Tags : ". $this->tags."\n";
        	    $mailSentResponse .= print_r($arr_output,true)."\n";
        	    $this->logEmailSentResponse($mailSentResponse);
        	}

        	if(is_array($arr_output)){
        		foreach($arr_output as $response){
        		    if(isset($response->code) && $response->code == 503) {
        		        $this->logEmailInfo();
        		    } else {
            			if($response->status == "rejected")
            			{
            				$this->addError($response->email ." rejected : ". $response->reject_reason);
            			}
            			elseif($response->status == "invalid")
            			{
            				$this->addError($response->email ." invalid : ". $response->reject_reason);
            			}
            			
            			$this->responseStatus = $response->status;
            			
            			$reject_reason = '';
            			if(isset($response->reject_reason)){
            			    $reject_reason = $response->reject_reason;
            			}
            			
            			$this->responseRejectReason = $reject_reason;
        		    }
        		}
        	}
        	else
        	{        	
        	    if(isset($arr_output->status) && $arr_output->status == "error")
        		{
        		    if(isset($arr_output->code) && $arr_output->code == 503) {
        		      $this->logEmailInfo();
        		    } else {
        		      $this->addError($arr_output->name .": ". $arr_output->message);
        		    }
        		}        	
        	}
        }
    
       
        
       if($this->hasErrors())
        {
            return false;
        }
        else
        {
            return true;
        }
    }
    
    public function logEmailSentResponse($logInfo) {

        $filename = APPLICATION_PATH ."/extras/logs/emailsentlog.txt";
        // Let's make sure the file exists and is writable first.
        if (is_writable($filename)) {
            if (!$handle = fopen($filename, 'a')) {
                $this->addError("Cannot open file ($filename)");
            }

            // Write $requestData to our opened file.
            if (fwrite($handle, $logInfo) === FALSE) {
                $this->addError("Cannot write to file ($filename)");
            }

            fclose($handle);
        } else {
            $this->addError("Email sent log is not writable");
        }
    }

    /**
     * Log failed 503 ServiceUnavailableError email request data into redis
     */
    public function logEmailInfo()
    {
        $redis = getRedisInstance();
        if(!empty($redis)){
            $redisFailedEmailKey = 'mandrillFailedEmails';
            $jsonEmailData = $this->requestData;
            $uniqEmailId = 'mandrill'.time();

            $emailLogData = $uniqEmailId."\n";
            $emailLogData .= "===========================================\n";
            $this->logEmailSentResponse($emailLogData);
            // store email data in json encoded format to Redis
            $redis->hmset($redisFailedEmailKey, $uniqEmailId, $jsonEmailData);
        } else {
            $this->addError("Error in making redis connection.");
        }
    }
    
    /**
     * It is called from /script/MailertestController.php to resend failed 503 ServiceUnavailableError email
     * stored into redis
     * @param json $jsonEmailData
     * @return boolean
     */
    public function resendfailedemails($jsonEmailData) {

        //API endpoint to check downtime response
        $ch = curl_init('https://mandrillapp.com/api/1.0/messages/send.json');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonEmailData);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $output = curl_exec($ch);

        /* curl_error function called before closing curl session
         If we call after closing curl session it throws notice */
        $curlError = '';
        if(curl_errno($ch)){
            $curlError = curl_error($ch);
        }

        curl_close($ch);
        if($output ===  FALSE)
        {
            $this->addError("Error connecting to Mandrill API ". $curlError);
        }
        else
        {
            $arr_output = json_decode($output);

            if(is_array($arr_output)){
                foreach($arr_output as $response){
                    if($response->status == "rejected")
                    {
                        $this->addError($response->email ." rejected : ". $response->reject_reason);
                    }
                    elseif($response->status == "invalid")
                    {
                        $this->addError($response->email ." invalid : ". $response->reject_reason);
                    }

                    $this->responseStatus = $response->status;

                    $reject_reason = '';
                    if(isset($response->reject_reason)){
                        $reject_reason = $response->reject_reason;
                    }

                    $this->responseRejectReason = $reject_reason;
                }
            }
            else
            {
                if(isset($arr_output->status) && $arr_output->status == "error") 
                {
                    $this->addError($arr_output->name .": ". $arr_output->message);
                }
            }
        }

        if($this->hasErrors())
        {
            return false;
        }
        else
        {
            return true;
        }

    }
}