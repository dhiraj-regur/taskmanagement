<?php

class ReminderController extends LMVC_Controller{



	public function init()

	{

		$this->setTitle(' Reminder');			

	}

  public function indexAction() {
    $host = "https://".$_SERVER['HTTP_HOST'];
    $appurl = $host.'/app';
    global $mailLogger;
    $tasks = new Models_Task();
    $lists = $tasks->getDueDateData();
    $reminders =array();
    foreach($lists as $list) {
            $reminders[$list['userId']][$list['pId']][] = $list;
    }
    foreach ($reminders as $userId => $userReminder) {
        foreach ($userReminder as $project) {
            $userName = $project[0]['userName']; 
            $email = $project[0]['email'];
            continue;
        }

        $content = 'Hi '.ucwords($userName).',';
        $content .= '<br> You have following task scheduled today.<br>';
        $content .= '<ol>';
        foreach ($userReminder as $project) {
            $projectLink = $appurl."?pId=".$project[0]['pId'];
            $content .= '<li>';
            $content .= '<a href="'.$projectLink.'">'.$project[0]['projectname'].'</a>';
            $content .= '<ul>';
            foreach ($project as $tasks) {
                $content .= '<li>';
                $content .= $tasks['task'].' - '.$tasks['duedate'];
                $content .= '</li>';
            }
            $content .= '</ul>';
            $content .= '</li>';
        }
        $content .= '</ol>';
        //send email here
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $from = "from@example.com";
        $to = $email;
        $subject = "Scheduled Task Today ". date("Y-m-d");
        $body = $content;

        $log = "=======================================================================\r\n ";
        $log .= "From: $from\r\n ";
        $log .= "To: $to\r\n ";
        $log .= "Subject: $subject\r\n ";
        
        $content = htmlspecialchars(trim(strip_tags($content)));
        $log .= "Body:\n $content"."\r\n\r\n";
        $mailLogger->log($log, true);

        $mailer = Helpers_Mailer_Factory::getMailer('PHPMailer');
        $rtn = $mailer->sendMail($from, $to, $subject,nl2br($body));

    }
    die();
  }

}