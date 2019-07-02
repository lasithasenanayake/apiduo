<?php

class redisConnectionPool {
    /**
         * Developer :Lasitha Senanayake
         * Date : May 1 2019
         * Comments: Redis Connection Pool
         * email :lasitha.senanayake@gmail.com
         * github : https://github.com/lasithasenanayake
         * company: Duo Software  
         */
    public static $cache;
    private $redis;

    public function getConnection(){
        if(!$this->redis){
            try {
                DavvagApiManager::log("redis","info","Start Open Connection");
                //$this->redis = new PredisClient();
                $connectionInfo = DavvagApiManager::$tenantConfiguration["configuration"]["redis"];
                $this->redis= new Redis(); 
                $this->redis->connect($connectionInfo["servername"], $connectionInfo["port"]); 
                redisConnectionPool::$cache=true;
                DavvagApiManager::log("redis","info","Finish Open Connection");
                return $this->redis;
            }
            catch (Exception $e) {
                redisConnectionPool::$cache=false;
                DavvagApiManager::log("redis","error",$e->getMessage());
                throw new Exception($e->getMessage());
                //die();
            }
            //DavvagApiManager::addAction("end", array($this,"closeConnection"));
        }

        return $this->redis;
    }

    public function closeConnection(){
        if($this->redis){
            //sqlsrv_close( $this->dbcon );
        }else{
            //DavvagApiManager::log("redis","error",$e->getMessage());
            throw new Exception("No Connection to Close.");
        }
    }

    
}