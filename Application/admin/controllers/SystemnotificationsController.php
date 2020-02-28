<?php

class Admin_SystemnotificationsController extends LMVC_Controller
{
    
    public function init()
    {
        $this->setTitle('System Notifications');
    }
    
    public function indexAction(){
        
    }
    
    public function listAction(){
        
        $this->setNoRenderer(true);
        $defaultFilters = array();
        
        $dt = new LMVC_DataTablesV2();
        $dt->setDBAdapter(LMVC_DB::getInstance()->getDB());
        $dt->setTable('system_notifications');
        
        $dt->setDefaultFilters($defaultFilters);
        $dt->setIdColumn('system_notifications.id');
        $dt->addColumns(array(
            'id' => 'id',
            'type' => 'type',
            'message' => 'message',
            'slug' => 'slug',
            'event' => 'event',
            'dateTime' => 'dateTime',
            'seen' => 'seen'
        ));
        
        $dt->registerRowProcessor('dgSysNotificationTable');
        $dt->getData();
    }
    
    public function getlatestunreadnotificationAction(){
        
        $this->setNoRenderer(true);
        global $db;
        
        $totalNotifications = 0;
        $sysNotifications = array();
        $messageData = array();
        
        $redisNotifications = json_decode(redis_fetch(REDIS_SYSTEM_NOTIFICATIONS_KEY),true);
        
        if(empty($redisNotifications)){
            
            $sql = "SELECT id, type, message, dateTime 
                    FROM system_notifications 
                    WHERE seen = 0 
                    ORDER BY id DESC";
            
            $data =  $db->getAll($sql, DB_FETCHMODE_ASSOC);
            
            $totalNotifications = count($data);
            
            $cnt = 1;
            if(!empty($data)){
                
                foreach($data as $row){
                    
                    if($cnt <= 10){
                        $messageData[] = array("id" => $row["id"], "type" => $row["type"], "message" => $row["message"], "dateTime" => $row["dateTime"]);
                    }
                    else{
                        break;
                    }
                    $cnt++;
                }
            }
            
            redis_store(REDIS_SYSTEM_NOTIFICATIONS_KEY, json_encode(array("totalNotifications" => $totalNotifications, "notifications" => $messageData)));
        }
        else{
            $totalNotifications = $redisNotifications["totalNotifications"];
            $messageData = $redisNotifications["notifications"];
        }
        
        $sysNotifications = array("totalNotifications" => $totalNotifications, "notifications" => $messageData);
        
        header('Content-type: application/json');
        echo json_encode($sysNotifications);
    }
    
    public function markasseenAction(){
        
        $status = 0;
        $message = "";
        $this->setNoRenderer(true);
        
        if($this->isPost()){
            
            $id = $this->getRequest()->getPostVar('id');
            
            $sysNotification = new Models_SystemNotification($id);
            if(!$sysNotification->isEmpty){
                
                $sysNotification->seen = 1;
                $recUpdated = $sysNotification->update(array("seen"));
                
                if($recUpdated){
                    $sysNotification->setRedisSytemNotifications();
                    $status = 1;
                    $message = "Notification status updated successfully";
                }
            }
        }
        
        if($status == 0){
            $message = "There is some error in updating the status";
        }
        
        $response = array('status'=>$status, 'message'=> $message);
        header('Content-type: application/json');
        echo json_encode($response);
    }
}

?>