<?php
require('functions.php');
# set time zone to UK
date_default_timezone_set('Europe/London');

# init front controller

$front = LMVC_Front::getInstance();
$front->enableCLI();
$front->setApplicationDirectory(APPLICATION_PATH);
$front->setControllerDirectory(array('cli' => '/cli/controllers/'));

$dbLogger = new LMVC_Logger();
$dbLogger->setLogFilePath(APPLICATION_PATH .'/extras/logs/dblog.txt',true);

#connect db

$db = LMVC_DB::getInstance()->connect(DB_HOST, DB_UNAME, DB_PWD, DB_NAME);

#configs
$siteConfig = new Models_SiteConfig();

#global sql data cache
$dataCache = Models_SQLCache::getInstance();


# set msyql time zone to UK (to sync with php)
$db->query('SET @@session.time_zone ="+00:00";');


//ob_start('minify_callback');
#dispatch now!
if(isset($argv))
{
	if( count($argv) > 2 )
	{
		//var_dump($argv);		
		$controller = $argv[1];
		$action = $argv[2];
		
		if(count($argv)>3)
		{		    
		    $options = $argv[3];
		    
		    if(strstr($options, "--dir="))
		    {
		        $tmp = explode('=', $options);
		        $directory = $tmp[1];
		        
		        if(!empty($directory))
		        {
		            $front->setControllerDirectory(array('cli' => '/cli/controllers/'. $directory));
		        }
		    }
		    else
		    {
		        echo "\n****************\n";
		        echo "Invalid option ". $options . ' specified. Posssible options are --dir=YOUR_DIR';
		        echo "\n****************\n\n";
		        die();
		    }
		}
		
		if(ENV === 'staging')
		{
			error_reporting(E_ALL ^ E_DEPRECATED ^ E_STRICT);
			ini_set('display_errors','On');
			ini_set("log_errors", 1);
			$errorLogFileName =   $controller ."_". $action .".log";			
			if(!empty($directory))
			{
				$errorLogFileName = $directory ."_". $errorLogFileName;
			}
			
			ini_set("error_log", MVC_PATH ."/Application/extras/logs/cli/". $errorLogFileName);
		}	
		elseif(ENV === 'production')
		{
			error_reporting(E_ALL ^ E_DEPRECATED ^ E_STRICT);
			ini_set('display_errors','off');
			ini_set("log_errors", 1);
			$errorLogFileName =   $controller ."_". $action .".log";			
			if(!empty($directory))
			{
				$errorLogFileName = $directory ."_". $errorLogFileName;
			}
			
			ini_set("error_log", MVC_PATH ."/Application/extras/logs/cli/". $errorLogFileName);
		}		
		
		$front->dispatch('cli',$controller,$action);
	}
	else
	{
		echo "\n****************\n";
		echo "Error: Not enough arguments supplied";
		echo "\n****************\n";
	}
}
else
{
	echo "\n****************\n";
	echo "Error: \$argv variable is not available. Check PHP.ini settings http://php.net/manual/en/reserved.variables.argv.php";
	echo "\n****************\n\n\n";
	die();
}




#destroy
unset($front);
?>