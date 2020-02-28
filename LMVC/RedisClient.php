<?php
/*
 *  Redis Client
 *  Initialize Redis, set data in redis, provide data from redis
 *  @author: Mitul
 */

class LMVC_RedisClient{
    
    private static $instance;
    private $redisInstance;
    
    public static function getInstance() {
        if (!isset(self::$instance)) {
            $c = __CLASS__;
            self::$instance = new $c;
            
            // Initialize Redis client
            Predis\Autoloader::register();
            self::$instance->redisInstance = new Predis\Client(array("host" => REDIS_HOST,"port" => REDIS_PORT));
        }
        
        
        return self::$instance;
    }
    
    public function getRedisInstance(){
        
        return $this->redisInstance;
    }
}
?>