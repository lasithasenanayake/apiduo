<?php
    Class CacheQuery{
        private $sqlUnit;

        function __construct($sqlcon){
            $this->sqlUnit= $sqlcon;
        }

        public function get($query){
            $cacheObj=CacheData::getObjects($query["key"],$query["object_name"]);
            if($cacheObj){
                $dbobj=$cacheObj;
                return $dbobj;
            }else{//$sql="SELECT Top (1) OpeningBalance,TranDateTime FROM a_LCOLedger  WHERE GULCOID = '". $this->lcoObject->GULCOID."' ORDER BY ID DESC";
                $dbobj= $this->sqlUnit->process($query["sql"]);
                if(isset($dbobj) && count($dbobj)>0){
                    CacheData::setObjects($query["key"],$query["object_name"],$dbobj);
                    return $dbobj;
                }else{
                    return array();
                }
            }
        }

        public function get_single($query){
            $cacheObj=CacheData::getObjects($query["key"]."-s",$query["object_name"]);
            if($cacheObj){
                $dbobj=$cacheObj;
                return $dbobj;
            }else{//$sql="SELECT Top (1) OpeningBalance,TranDateTime FROM a_LCOLedger  WHERE GULCOID = '". $this->lcoObject->GULCOID."' ORDER BY ID DESC";
                $dbobj= $this->sqlUnit->process($query["sql"]);
                if(count($dbobj)>0){
                    CacheData::setObjects($query["key"]."-s",$query["object_name"],$dbobj[0]);
                    return $dbobj[0];
                }else{
                    return null;
                }
            }
        }
    }
?>