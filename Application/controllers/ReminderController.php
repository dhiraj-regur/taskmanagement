<?php

class ReminderController extends LMVC_Controller{



	public function init()

	{

		$this->setTitle(' Reminder');			

	}

  public function indexAction() {
    //get 
    //$host = $_SERVER['HTTP_HOST'];
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
            $content .= '<li>';
            $content .= '<strong>'.$project[0]['projectname'].'</strong>';//TODO- add project link
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
        $from = "welcome@pinlocal.com";
        $to = 'dhiraj.tekade@regur.net';//$email
        $subject = "Scheduled Task Today ". date("Y-m-d");
        $body = $content;        
        mail($to,$subject,$body,$headers);
        
        //$mailer = Helpers_Mailer_Factory::getMailer('Mandrill');
        //$mailer->setApiKey('3ACQi8WOxJy40zQ08wYeHw');
       // $mailer->setTags('task_schedule');
       // $rtn = $mailer->sendMail($from, $to, $subject,nl2br($body));

      // echo $content;
    }
    die();
  }

}