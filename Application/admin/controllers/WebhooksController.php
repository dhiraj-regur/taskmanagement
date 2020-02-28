<?php
class Admin_WebhooksController extends LMVC_Controller
{
	public function init()
	{
		$this->setTitle('Webhooks');	
	}
	
	public function indexAction()
	{
		$this->registerHeaderScript('/admin_resources/js/companywebhooksettingsctrl.js');
		
		global $siteConfig;
		$company = new Models_Company();
		$companies = $company->getAll(array('id', 'companyName'), null, array("companyName" => "ASC"), null, null, true, DB_FETCHMODE_ASSOC);
		$companiesAll = array();
		foreach ($companies as $company) {
			$companiesAll[$company['id']] = $company['companyName'];
		}
		$this->setViewVar('companies', $companiesAll);	
		
		
	}
	
	public function listAction()
	{
		$this->setNoRenderer(true);
		
		$dt = new LMVC_DataTablesV2();
		$dt->setDBAdapter(LMVC_DB::getInstance()->getDB());
		$dt->setTable('company_webhooks');
		$dt->setIdColumn('companies.id');
		$dt->setJoins("INNER JOIN companies ON company_webhooks.companyId=companies.id");
		$dt->addColumns(
				array(	'companyWebhookId'=>'company_webhooks.id',
						'companyId'=>'companyId',
						'companyName'=>'companyName',
						'event'=>'event',
						'webhookUrl'=>'webhookUrl',
						'webhookKey'=>'webhookKey'
				));
		$dt->getData();
		die();	
		
	}
	
	public function editAction()
	{
		$this->setNoRenderer(true);		
		$data = array();
		$id = $this->getRequest()->getParam('id');				 
		$response = new LMVC_AjaxResponse('json');
		$webhook = new Models_CompanyWebhooks($id);
		
		if(!$webhook->isEmpty)
		{
				$data = array(
					'id'=> $webhook->id,
					'companyId' => $webhook->companyId,
	           		'event' => $webhook->event,
					'webhookUrl' => $webhook->webhookUrl,
					'webhookKey' => $webhook->webhookKey				
				);		
		}
		else
		{
			$response->addError("Invalid Id");
		}
			
		$response->setData(array('webhookData'=>$data));
		$response->output();
	}
	
	public function savesettingsAction()
	{		
		$this->setNoRenderer(true);	
		$data = array();
		$response = new LMVC_AjaxResponse('json');
		$id = $this->getRequest()->getPostVar('id');
		$companyId =  $this->getRequest()->getPostVar('companyId');		
		$event = $this->getRequest()->getPostVar('event');
		$whUrl = $this->getRequest()->getPostVar('webhookUrl');
		$whKey = $this->getRequest()->getPostVar('webhookKey');		
		
		$webhook = new Models_CompanyWebhooks($id);
		
		$webhook->companyId = $companyId;
		$webhook->event = $event;
		$webhook->webhookUrl = $whUrl;		
		$webhook->webhookKey = $whKey;
		
		
		if($webhook->validate())
		{
			if($webhook->isEmpty)
			{
				$webhook->create();
				$response->setMessage("Webhook added successfully");
			}
			else
			{				
				$id = $webhook->update();
				if($id>0)
				{	
					$data = array(
							'id'=> $webhook->id,
							'companyId' => $webhook->companyId,
							'event' => $webhook->event,
							'webhookUrl' => $webhook->webhookUrl,
							'webhookKey' => $webhook->webhookKey
					);
					$response->setMessage("Webhook updated successfully");
				}
				else
				{
					$response->addError("Error in update record");
				}	
			}
			$status = 1;
		}
		else
		{
			if($webhook->hasErrors())
			{				
				$errors = $webhook->getErrors();
				foreach ($errors as $error)
				{
					$response->addError($error);					
				}
			 }
		}
		
		$response->setData(array('webhookData'=>$data));
		$response->output();	
	}
	
	public function deleteAction()
	{
		$this->setNoRenderer(true);
		$response = new LMVC_AjaxResponse('json');	
		$id = $this->getRequest()->getParam('id');
		 		
		$webhook = new Models_CompanyWebhooks($id);
		
		if(!$webhook->isEmpty)
		{
			 $webhook->delete();			
			 $response->setMessage("Record deleted successfully");
		}
		else
		{
			$response->addError("No record found");
		}
		
		$response->output();
	}
	
	public function generatekeyAction()
	{
		$response = new LMVC_AjaxResponse('json');		
		$this->setNoRenderer(true);			
		$key = $this->generateKey();		
		$response->setData(array('key'=>$key));
		$response->output();
	}
	
	private function generateKey()
	{
		$randomStr = rand().time();
		return hash ('sha1',rand());		
	}
	
	public function sendtestleadAction()
	{
	    $this->setNoRenderer(true);
	    global $db;
	    $data = array();
	    
	    $response = new LMVC_AjaxResponse('json');
	    $leadid = $this->getRequest()->getPostVar('leadId',"numeric",0);
	    $companyId = $this->getRequest()->getPostVar('companyId',"numeric",0);
	    
	    //check Lead id And Company Id Exist In Out Datadase
	    $lead = new Models_Lead($leadid);
	    $company = new Models_Company($companyId);
	    
	    if(!$lead->isEmpty && !$company->isEmpty)
	    {
	        $rtn = $db->query("INSERT INTO webhook_queue(companyId,leadId,queuedDateTime,event,attempts) VALUES(".$companyId.",".$leadid.",'". date("Y-m-d H:i:s", time()) ."','test_lead',0)");	        
	        if($rtn)
    	    {
    	        $queueId = $db->getOne("SELECT LAST_INSERT_ID()");    	        
    	        $webhookProcessor = new Service_WebhookProcessor();
    	        $result = $webhookProcessor->getWebhookQueueData('na','test_lead',$queueId);
   
    	        if (!empty($result))
    	        {
    	            
    	            $webhookProcessor->postData($result);
    	            $response->setMessage("Test lead submitted successfully. Please <a href='/admin/webhooklogs/?companyid=".$companyId."&leadevent=test_lead' target='_blank'>click here</a> to check the status.");
    	            
    	        }
    	        else
    	        {
    	            $response->addError("No test lead found");
    	        }
    	        
    	    }
    	    else
    	    {
    	        $response->addError("Test lead not sent!");
    	    }
	    }
	    else
	    {
	        $response->addError("Lead ID or Company does not exist");
	    }
	    
	    $response->output();
	}
	
}