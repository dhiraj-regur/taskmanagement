<?php

class Admin_WebhooklogsController extends LMVC_Controller {
	
	public function init() {
		
		//Setting title
		$this->setTitle('Webhook Logs');
	
	}
	
	
	public function indexAction() {
		
		
		global $db;
		
				 
		//Getting the id of the company, to get the webhook logs of that company
		$companyId = $this->getRequest()->getVar('companyid', 'numeric', '0');
		
		//Get Lead event And Set Lead Events Array
		$leadEvents= array("new_lead"=>"New Lead","test_lead"=>"Test Lead");
		$leadevent = $this->getRequest()->getVar('leadevent');
		
		$sql = "SELECT a.id, a.companyName FROM companies a INNER JOIN company_webhooks b on b.companyId = a.id ORDER BY companyName";
		$companies = $db->getAll($sql, DB_FETCHMODE_ASSOC);
		$allCompaniesNames = array();
		
		foreach ($companies as $company) {
			$allCompaniesNames[$company['id']] = $company['companyName']."  (".$company['id'].")";
		}
		 
		$this->setViewVar('allCompaniesNames',$allCompaniesNames);
				 
		$this->setViewVar('companyId',$companyId);
		
		$this->setViewVar("leadEvents",$leadEvents);
		
		$this->setViewVar("leadevent",$leadevent);
	}
	
	
	public function listAction() {		
		
		$defaultFilters = array();
		
		$dt = new LMVC_DataTablesV2();
		$dt->setDBAdapter(LMVC_DB::getInstance()->getDB());
		
		//Getting the id of the company, to get the webhook logs of that company
		$companyId = $this->getRequest()->getParam('companyid');
		//Get Lead Type
		$leadEvent = $this->getRequest()->getParam('leadevent');
				
		if(!empty($companyId) && !empty($leadEvent))
		{
		  $defaultFilters['companyId'] = "'$companyId'";
		  $defaultFilters['event'] = "'$leadEvent'";
		}
		
		if (!empty($defaultFilters)) {
			$dt->setDefaultFilters($defaultFilters);
		}
		
		$dt->setTable('webhook_queue');		
		$dt->setIdColumn('id');		
		$dt->addColumns(array('id'=>'id',
				'leadId'=>'leadId',
				'queuedDateTime'=>'queuedDateTime',
				'processed'=>'processed',
				'status'=>'status',
				'attempts'=>'attempts',
				'inProcess'=>'inProcess',
				'nextAttemptDateTime'=>'nextAttemptDateTime'));
		$dt->getData();		
		die();
	}
	
	
	public function detailsAction(){
		
		//Setting default value to 0
		$webhookQueueId = 0;
			
		$webhookQueueId = $this->getRequest()->getParam('webhookqueueid');
		
		//Getting the attempts based on the webhookQueueId
		$sql = "SELECT * from webhook_attempts_log WHERE webhookQueueId=".$webhookQueueId ;
		
		
		$db = LMVC_DB::getInstance()->getDB();
		$webhookAttemptsDetails = $db->getAll($sql, DB_FETCHMODE_ASSOC);
				
		$totalAttempts = count($webhookAttemptsDetails);
				
		//Replacing the <pre> from the response and also converting characters to HTML entities.
		for($i=0;$i<$totalAttempts;$i++){
			$webhookAttemptsDetails[$i]['response'] = htmlentities(str_replace(array('<pre>', '</pre>'),array('',''),$webhookAttemptsDetails[$i]['response']));
		}
				
		$this->setViewVar('webhookAttemptsDetails',$webhookAttemptsDetails);
		
	}
	
}