<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Penuel
 * Date: 30/5/13
 * Time: 1:33 PM
 * To change this template use File | Settings | File Templates.
 */

require_once(dirname(__FILE__) ."/class.phpmailer.php");

class Helpers_Mailer_PHPMailer extends Helpers_Mailer_Abstract
{

    private $phpMailer;

    public function init()
    {
        parent::init();
        $phpMailer = new PHPMailer();
    }

    public function send()
    {
        $phpMailer = new PHPMailer();
        $email = $this->getRecipients()[0]['email'];
        $name = $this->getRecipients()[0]['name'];
        $body = $this->getHtmlBody();
        $from = $this->getSender()['email'];
        $phpMailer->Subject = "A Transactional Email From Pepipost";
        $phpMailer->MsgHTML($body);
        $phpMailer->AddAddress($email,$name);
        $phpMailer->send();

    }


}