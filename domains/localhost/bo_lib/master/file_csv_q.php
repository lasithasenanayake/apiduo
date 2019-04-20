<?php
Class LCO_Pending{
    public function addLCO($lco){
        $cacheObj=CacheData::getObjects_fullcache("qrequest","request_file_csv");
            if(!isset($cacheObj)){
                $cacheObj=array();
            }
            foreach ($cacheObj as $key => $value) {
                # code...
                if($value->GULCOID==$lco){
                    return "Already Request is in please wait while csv process.\n";
                }
            }
            if(!isset($cacheObj2)){
                $cacheObj2=array();
            }
            $cacheObj2=CacheData::getObjects_fullcache("qrequest_taken","request_file_csv");
            foreach ($cacheObj2 as $key => $value) {
                # code...
                if($value->GULCOID==$lco){
                    return "Already Request is in please wait while csv process.\n";
                }
            }
            $r= new stdClass();
            $r->GULCOID=$lco;
            $r->ENTITY=ENTITY;
            $r->status="pending";
            array_push($cacheObj,$r);
            //var_dump($cacheObj);
            CacheData::setObjects("qrequest","request_file_csv",$cacheObj);
            return "Requested csv has been put to processing que it will be generated shortly.\n";
            
    }

    public function getLCO(){
        $cacheObj=CacheData::getObjects_fullcache("qrequest","request_file_csv");
        CacheData::setObjects("qrequest","request_file_csv",array());
        CacheData::setObjects("qrequest_taken","request_file_csv",$cacheObj);
        return $cacheObj;
    }
}