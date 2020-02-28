<?php

class Admin_SfsyncqueuelogsController extends LMVC_Controller {
	
	public function init() {
		
		//Setting title
		$this->setTitle('Sfsyncqueue Logs');
	
	}
	
	
	public function indexAction() {
		
		
		
	}
	
	public function listAction() {
	
		$this->setNoRenderer(true);
		
		$dt = new LMVC_DataTablesV2();
		$dt->setDBAdapter(LMVC_DB::getInstance()->getDB());
		
		$dt->setTable('sf_sync_queue');
		$dt->setIdColumn('id');
		$dt->addColumns(array('id'=>'id',
				'object'=>'object',
				'operation'=>'operation',
				'recordId'=>'recordId',
				'status'=>'processed',
				'status'=>'status',
				'batchId'=>'batchId',
				'queuedDateTime'=>'queuedDateTime',
				'attempts' => 'attempts',
				'nextAttemptDateTime' => 'nextAttemptDateTime'));
		$dt->getData();
		
	}
	
	public function detailAction() {
		
		$sfsyncQueueId = $this->getRequest()->getParam('sfsyncqueueid','numeric',0);
		$sql = "SELECT * from sf_sync_queue_attempts_log WHERE syncQueueId = {$sfsyncQueueId} ORDER BY id desc";
		
		$db = LMVC_DB::getInstance()->getDB();
		$sfsyncQueueDetails = $db->getAll($sql, DB_FETCHMODE_ASSOC);
		
		$this->setViewVar('sfsyncQueueDetails',$sfsyncQueueDetails);
		
	}
	
	
}