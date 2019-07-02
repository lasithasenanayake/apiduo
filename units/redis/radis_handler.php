<?php

require_once  dirname(__FILE__) .  "/redisConnectionPool.php";


class redisHandler extends AbstractUnit {
    /**
         * Developer :Lasitha Senanayake
         * Date : May 1 2019
         * Comments: Redis Handler
         * email :lasitha.senanayake@gmail.com
         * github : https://github.com/lasithasenanayake
         * company: Duo Software  
         */
    private static $connpool;
    private $redis;
    /*
    public function Open(){
        
    }*/

    public function process ($input,$operation=null){
        if (!isset(redisHandler::$connpool))
            redisHandler::$connpool = new redisConnectionPool();

        $this->redis = redisHandler::$connpool->getConnection();
        
        if(!isset($operation))
            $operation = $this->getUrnInput();
        
            switch ($operation){
                case "get":
                if(redisConnectionPool::$cache){
                    $key=md5(ENTITY."-".$input->key);
                    $items=$this->redis->get($key);
                    if($items==false){
                        return null;
                    }else{
                        return $items;
                    }
                }else{
                    return null;
                }
                case "set":
                if(redisConnectionPool::$cache){
                    $key=md5(ENTITY."-".$input->key);
                    $this->redis->set($key,$input->object);
                }
                case "update":
                    //return $this->update();
                case "delete":
                    //return $this->delete();
                case "excute":
                    //return $this->excute($input);
                case "bulksend":
                   // return $this->BulkSend($input);
            }


    }
    private function Init(){
        if(!isset($redis)){
            if (!isset(redisHandler::$connpool))
                redisHandler::$connpool = new redisConnectionPool();

            $this->redis = redisHandler::$connpool->getConnection();
        }
    }

    public function get($key){
        $this->Init();
        if(redisConnectionPool::$cache){
            $key=md5(ENTITY."-".$key);
            $items=$this->redis->get($key);
            if($items==false){
                return null;
            }else{
                return $items;
            }
        }else{
            return null;
        }
    }

    public function delete($key){
        $this->Init();
        if(redisConnectionPool::$cache){
            $key=md5(ENTITY."-".$key);
            $this->redis->delete($key);
            
        }
    }

    public function set($key,$objectstr){
        $this->Init();
        if(redisConnectionPool::$cache){
            $key=md5(ENTITY."-".$key);
            $this->redis->set($key,$objectstr);
        }
    }

}

?>