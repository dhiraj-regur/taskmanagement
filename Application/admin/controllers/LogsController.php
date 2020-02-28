<?php
class Admin_LogsController extends LMVC_Controller{
	
	public function init(){		

		$this->setTitle('Logs');

		array_push($this->breadcrumb, array("label" => "Home", "url" => "/admin/logs"));
		array_push($this->breadcrumb, array("label" => "Logs", "url" => "/admin/logs/logsviewer?directory="));
		$this->setViewVar('breadcrumb', $this->breadcrumb);

		if($this->isPost()){
			$dir = $this->getRequest()->getPostVar('directory');
		}	
		else{
			$dir = $this->getRequest()->getVar('directory');
		}
		
		if(isset($dir) && !empty($dir)){
			if(strncmp($dir, "..", 2) == 0){
				exit("Directory does not exists");
			}	
		}
	}
	
	public function indexAction()
	{
	    
	}
	
	public function phplogAction()
	{
	    $date = $this->getRequest()->getVar('date','string', date('Y-m-d'));
	    $logFileName = "pinlocal-system-".$date.".log";
	    $logFile = APPLICATION_PATH .'/extras/logs/web/'. $logFileName; //quick and dirty
	    if(file_exists($logFile)){
	        
	        $log = file_get_contents($logFile);
	       
	        $this->setViewVar('log', $log);
	    }
	    else
	    {
	        echo $logFile ." does not exist";
	    }
	  
	    
	}
	
	

    public function agentassignmentsAction()
    {
 
        $date = $this->getRequest()->getVar('date','string', date('Y-m-d'));
        $logFileName = "lead_agent_assignment_history_".$date.".html";        
        $logFile = APPLICATION_PATH .'/extras/logs/'. $logFileName; //quick and dirty
        if(file_exists($logFile)){
            $log = array_map('str_getcsv', file($logFile));
            $this->setViewVar('log', $log);
        }
       
    }
    
    
    public function agentassignmentsfulllogAction()
    {
        $date = $this->getRequest()->getVar('date','string', date('Y-m-d'));
        $logFileName = "lead_assigne_to_user_log_".$date.".html";
        $logFile = APPLICATION_PATH .'/extras/logs/'. $logFileName; //quick and dirty
        if(file_exists($logFile)){
            
            $log = file_get_contents($logFile);
            
            $this->setViewVar('log', $log);
        }
        else
        {
            echo $logFile ." does not exist";
        }
        
        
    }
    
    
    public function redisagentleadsqueueAction()
    {
       
        $redis = getRedisInstance();
        $log = array();
        if(!empty($redis)){
         
            $newLeads = $redis->hgetall(REDIS_LEAD_QUEUE_FOR_AGENT_KEY);
            
            foreach($newLeads as $leadId => $jsonData){
                
                $data = json_decode($jsonData,true);                
                array_push($log, $data);
                
            }
         
            $this->setViewVar('log', $log);
            
            
           
        }
     
                  
        
    }
    
    public function companyleadassignmentlogAction(){
        
        $leadId = $this->getRequest()->getParam('id','numeric',0);
        
        if(!empty($leadId)){
            
            $logFileName = "lead_".$leadId.".txt";
            $logFile = APPLICATION_PATH .'/extras/logs/lead_assignments/'. $logFileName;
            if(file_exists($logFile)){
                
                $log = file_get_contents($logFile);
                
                $this->setViewVar('log', $log);
            }
            else
            {
                echo $logFile ." does not exist";
            }
        }
        else{
            echo "Invalid Lead Id";
        }
    }
    
	public function logsviewerAction(){
		$files = array_diff(scandir(SYSTEM_LOGS_PATH), array("..", "."));
		$directories = array();
		$dirUrl = "";
		
		$params = $this->getRequest()->getGet();
		
		if(isset($params)){			
			if(isset($params['directory'])){
				$dirPath = SYSTEM_LOGS_PATH . $params['directory'];
				
				if(is_dir($dirPath)){	
					$files = array_diff(scandir(urldecode($dirPath)), array("..", "."));
					$dirUrl .= urldecode($params['directory']) . '/';
				
					foreach(explode("/", $params['directory']) as $dir){
						if(!empty($dir)){
							$url = end($this->breadcrumb);
							array_push($this->breadcrumb, array("label" => $dir, "url" => $url["url"] . urlencode($dir . "/") ));
							$this->setViewVar('breadcrumb', $this->breadcrumb);
						}
					}
					
					if(isset($params['file']) && !empty($params['file'])){
						$file = SYSTEM_LOGS_PATH . '/' . $params['directory'] . $params['file'];
						if(file_exists($file)){
							array_push($this->breadcrumb, array("label" => $params['file'], "url" => ""));
							$this->setViewVar('breadcrumb', $this->breadcrumb);
								
							$log = file_get_contents($file);
							if(empty($log))
								$log = "{file is empty}";
							$this->setViewVar('log', $log);
						}
						else
							$this->setViewVar("message", $file . " file does not exists");
					}
				}
				else 
					$this->setViewVar("message", $dirPath . " directory does not exists");
			}			
		}
		
		$fileStats = array();
		$sortedFiles = array();
		// separate files and directories
		foreach($files as $file){
			if(is_dir(SYSTEM_LOGS_PATH . '/' . $dirUrl . $file)){
				array_push($directories, $file);
				$index = array_search($file, $files);
				unset($files[$index]);
			}
			else{
				$fileStats[$file] = stat(SYSTEM_LOGS_PATH . '/' . $dirUrl . $file);
				$sortedFiles[$file] = $fileStats[$file]['mtime'];
				
				$fileStats[$file]['mtime'] = trim(date("d-m-Y H:i:s", $fileStats[$file]['mtime']));
				$fileStats[$file]['sizeunit'] = "bytes";
				
				if($fileStats[$file]['size'] > (1024 * 1024)){
					$fileStats[$file]['size'] = round($fileStats[$file]['size'] / (1024 * 1024), 2);
					$fileStats[$file]['sizeunit'] = "MB";
				}
				
				else if($fileStats[$file]['size'] > 1024){
					$fileStats[$file]['size'] = round($fileStats[$file]['size'] / 1024, 2);
					$fileStats[$file]['sizeunit'] = "KB";
				}
			}
		}
		
		natcasesort($directories);		
		arsort($sortedFiles);
		
		$this->setViewVar('fileStats', $fileStats);
		$this->setViewVar('dirUrl', urlencode($dirUrl));
		$this->setViewVar('files', $sortedFiles);
		$this->setViewVar('directories', $directories);
	}    
	
	public function downloadAction(){
		$this->setNoRenderer(true);
		$params = $this->getRequest()->getGet();		
		
		if(isset($params['file']) && !empty($params['file'])){
			$file = SYSTEM_LOGS_PATH . '/' . urldecode($params['directory'] . $params['file']);
			if(file_exists($file)){
				$filename = end(explode("/", $file));
				header("content-Disposition: attachment; filename=$filename");
				readfile($file);
			}
			else{
				echo "File does not exists<br><br>";
				if(isset($params['retUrl']))
					echo "<a href='/admin/logs/logsviewer?directory=" . $params['retUrl'] . "'>back</a>";
			}				
		}
		else 
			echo "File does not exists.";
	}
	
	public function deleteAction(){
		$this->setNoRenderer(true);
		$filename = $this->getRequest()->getPostVar('file', 'string', '');
		$directory = $this->getRequest()->getPostVar('directory', 'string', '');
		
		if(isset($filename) && !empty($filename)){
			$file = SYSTEM_LOGS_PATH . '/' . urldecode($directory) . '/' . $filename;
			if(file_exists($file)){
				unlink($file);
				echo $filename . " deleted successfully.";
			}
			else 
				echo $filename . " file does not exists.";
		}
	}
	
	public function renameAction(){
		$this->setNoRenderer(true);
		
		$oldFilename = urldecode($this->getRequest()->getPostVar('oldFilename', 'string', ''));
		$newFilename = urldecode($this->getRequest()->getPostVar('newFilename', 'string', ''));
		$directory = urldecode($this->getRequest()->getPostVar('directory', 'string', ''));
	
		if(isset($oldFilename) && !empty($oldFilename)){
			$oldFile = SYSTEM_LOGS_PATH . '/' . $directory . '/' . $oldFilename;
			$newFile = SYSTEM_LOGS_PATH . '/' . $directory . '/' . $newFilename;
			
			if(file_exists($oldFile)){
				rename($oldFile, $newFile);
				echo "File renamed successfully.";
			}
			else
				echo "File does not exists";
		}
	}	
}
?>