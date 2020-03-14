<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Penuel
 * Date: 31/5/13
 * Time: 3:20 PM
 * To change this template use File | Settings | File Templates.
 */


class Helpers_Mailer_Factory {

    private static $mailer;
    private static $mailerClass;



    public static function  getMailer($className='',$init=true)
    {
        if(empty($className))
        {
            $class =   self::$mailerClass;
        }
        else
        {
            $class = "Helpers_Mailer_". $className;
        }

        if (!isset(self::$mailer)) {    //mailer is being created for first time
            self::$mailer = new $class;
        }
        else{
           if(get_class(self::$mailer) != $class ) //previous mailer is not same as current one
           {
               self::$mailer = new $class;  //initialize with new mailer class
           }
        }

        self::$mailerClass = $class;
        if($init) self::$mailer->init();
        return self::$mailer;
    }
    
    public static function destroy(){
        self::$mailer = NULL;
    }



}