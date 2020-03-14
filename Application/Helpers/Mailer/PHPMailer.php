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

    }
}