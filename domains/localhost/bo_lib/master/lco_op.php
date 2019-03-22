<?php
Class lco{
    private $sqlUnit;
    private $lcoObject;

    function __construct($sqlcon){
        $this->sqlUnit=$sqlcon;
    }

    private function get_lco($column,$value){
        $cacheObj=CacheData::getObjects_fullcache($column.'-'.$value,"m_CODealerMaster");
        if(!$cacheObj){
            $sql="";
            if($column!="vcno"){
                $sql="SELECT * FROM m_CODealerMaster  WHERE ".$column." = '".$value."'";
            }else{
                $sql="SELECT dbo.m_CODealerMaster.* FROM I_SerialNumber_Header  with (nolock)
                INNER JOIN m_StoresInformation with (nolock) ON I_SerialNumber_Header.GUStoreID = m_StoresInformation.GUStoreID
                INNER JOIN m_CODealerMaster with (nolock) ON m_StoresInformation.GULCOID = m_CODealerMaster.GULCOID
                WHERE I_SerialNumber_Header.SerialNumber = '".$value."'";
            }
            $dbobj= $this->sqlUnit->process($sql);
            if(count($dbobj)>0){
                CacheData::setObjects($column.'-'.$value,"m_CODealerMaster",$dbobj);
            }
            return $dbobj;
        }else{
            return $cacheObj;
        }
    }

    public function get_lcobycode($lcocode){
        $dbobj= $this->get_lco("LCOCode",$lcocode);
        if(count($dbobj)>0){
            $this->lcoObject=$dbobj[0];
            return $this->lcoObject;
        }else{
            throw new Exception('LCO was not found');
        }
    }

    public function get_lcobyvcno($lcocode){
        $dbobj= $this->get_lco("vcno",$lcocode);
        if(count($dbobj)>0){
            $this->lcoObject=$dbobj[0];
            return $this->lcoObject;
        }else{
            throw new Exception('LCO was not found');
        }
    }

    public function get_balance(){
        if($this->lcoObject){
            $cacheObj=CacheData::getObjects($this->lcoObject->GULCOID,"DealerMaster_Balance");
            if($cacheObj){
                $dbobj=$cacheObj;
            }else{
                $sql="SELECT Top (1) OpeningBalance,TranDateTime FROM a_LCOLedger  WHERE GULCOID = '". $this->lcoObject->GULCOID."' ORDER BY ID DESC";
                $dbobj= $this->sqlUnit->process($sql);
                CacheData::setObjects($this->lcoObject->GULCOID,"DealerMaster_Balance",$dbobj);
            }
            if(count($dbobj)>0){
                return floatval($dbobj[0]->OpeningBalance);
            }else{
                return 0;
            }
        }else{
            throw new Exception('LCO was not initialized to query balance');
        }
    }
}
?>