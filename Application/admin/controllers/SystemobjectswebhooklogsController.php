<?php

class Admin_SystemobjectswebhooklogsController extends LMVC_Controller {
	
	public function init() {
		
		//Setting title
		$this->setTitle('System Objects Webhook Logs');
	
	}
	
	
	public function indexAction() {
		
	}
	
	
	public function listAction() {		
		
		$dt = new LMVC_DataTablesV2();
		$dt->setDBAdapter(LMVC_DB::getInstance()->getDB());
		
		$dt->setTable('system_objects_webhook_queue');		
		$dt->setIdColumn('id');		
		$dt->addColumns(array('id'=>'id',
				'recordId'=>'recordId',
				'objectName'=>'objectName',
				'action'=>'action',
				'createdDateTime'=>'createdDateTime',
				'status'=>'status',
				'attempts'=>'attempts',
				'nextAttemptDateTime'=>'nextAttemptDateTime',
				'targetSystem' => 'targetSystem'));
		
		$dt->getData();
				
		die();
	}
	
	
	public function detailsAction(){
		
		global $db;
		
		$id = $this->getRequest()->getParam("id","numeric",0);
		$sql = "SELECT a.*, b.recordId, b.objectName, b.action,b.status, b.targetSystem FROM system_objects_webhook_queue_attempts_log a
				INNER JOIN system_objects_webhook_queue b ON b.id=a.systemObjectsWebhookQueueId
				WHERE systemObjectsWebhookQueueId = $id ORDER BY a.id DESC";
		
		$details = $db->getAll($sql, DB_FETCHMODE_ASSOC);
		
		$this->setViewVar('attemptsDetails',$details);
	}
	
}