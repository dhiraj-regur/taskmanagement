<?php
class Admin_DbbackupController extends LMVC_Controller{
	
	
	public function downloadAction(){
		
		// Make sure program execution doesn't time out
		// Set maximum script execution time in seconds (0 means no limit)
		set_time_limit(0);
		
		$error_list = array();
		$downloadWhat = $this->getRequest()->getPostVar('btnAction');
		
		if($this->isPost()){
		    
		          
		    
		    
		        if($downloadWhat == 'Full DB Backup')
		        {
		            $file = DB_BACKUP_FILE_PATH;
		        }
		        else if($downloadWhat == 'Only Structure')
		        {
		            $file = SITE_ROOT .'/_data/pinlocal_structure.sql.gz';
		        }
		        else if($downloadWhat == 'Compact DB')
		        {
		            $file = SITE_ROOT .'/_data/pinlocal_data.sql.gz';
		        }
		    
						
				
				
				//If file Exists
				if (file_exists($file)) {
					
					ob_clean();
					
					header("Content-Type: application/octet-stream");
					header('Content-Disposition: attachment; filename="'.basename($file).'"');
					header('Expires: 0');
					header('Cache-Control: public, must-revalidate, post-check=0, pre-check=0');
					header('Pragma: public');
					header('Content-Length: ' . filesize($file));
					
					ob_flush();
					flush();
					
					@readfile($file) or die("Error in reading file.");					
					exit();
					
				}else{
					
					array_push($error_list,'File Doesn\'t Exists. Please Check the path' );
					
				}
		}
		
		$this->setViewVar('error_list',$error_list);
		
	}
	
}

?>