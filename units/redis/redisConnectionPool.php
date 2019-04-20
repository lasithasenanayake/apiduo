<?php

class redisConnectionPool {
    public static $cache;
    private $redis;

    public function getConnection(){
        if(!$this->redis){
            try {
                //$this->redis = new PredisClient();
                $this->redis= new Redis(); 
                $this->redis->connect('172.16.8.70', 6379); 
                redisConnectionPool::$cache=true;
                return $this->redis;
            }
            catch (Exception $e) {
                redisConnectionPool::$cache=false;
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
            throw new Exception("No Connection to Close.");
        }
    }

    
}