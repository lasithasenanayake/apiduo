<?php
Class lco{
    private $sqlUnit;
    private $lcoObject;

    function __construct($sqlcon){
        $this->sqlUnit=$sqlcon;
    }

    public function get_lco($column,$value){
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

    public function get_expiring_vcnos($lastid,$lcocode,$days){
        require_once("cachequery.php");
        $cobj =new CacheQuery($this->sqlUnit);
        $query["key"] = $lastid."-".$lcocode."21".$days;
        $query["object_name"] = "accounts_exp";
        $query["sql"] = "SELECT  top 20 m_CODealerMaster.LCOCode,CAS_SubscriberAmendments .VCNo ,CAS_SubscriberAmendments.ScheduleDate ,CAS_SubscriberAmendments.AmendmentType,CAS_SubscriberAmendments.ID
        FROM            m_StoresInformation INNER JOIN
                                 I_SerialNumber_Header ON m_StoresInformation.GUStoreID = I_SerialNumber_Header.GUStoreID INNER JOIN
                                 CAS_SubscriberAmendments ON I_SerialNumber_Header.SerialNumber = CAS_SubscriberAmendments.STBID INNER JOIN
                                 m_CODealerMaster ON m_StoresInformation.GULCOID = m_CODealerMaster.GULCOID
        WHERE         CAS_SubscriberAmendments.ScheduleDate >= getdate() and DATEDIFF(DAY, getdate(), CAS_SubscriberAmendments.ScheduleDate) <= ".$days." AND (CAS_SubscriberAmendments.ID > ".$lastid.")  and CAS_SubscriberAmendments.AmendmentType in ('Cancel Service','Suspend Subscriber')and (m_CODealerMaster.LCOCode ='".$lcocode."')
        ORDER BY CAS_SubscriberAmendments.ID";
        return $this->account=$cobj->get($query);
    }

    public function get_lcos_accounts($lastid,$lcocode){
        require_once("cachequery.php");
        $cobj =new CacheQuery($this->sqlUnit);
        $query["key"] = $lastid."-".$lcocode."20";
        $query["object_name"] = "accounts_lco";
        $query["sql"] = "Select top 20 *
        From (SELECT        row_number()over (order by a_CustAccountInformation.AccountNo) as rnbr ,m_CODealerMaster.LCOCode, a_CustAccountInformation.AccountNo, a_CustAccountInformation.ServiceStatus,  a_CustAccountInformation.SIMID as VCNo, 
                a_CustAccountInformation.STBID
        FROM            I_SerialNumber_Header INNER JOIN
                                 m_StoresInformation ON I_SerialNumber_Header.GUStoreID = m_StoresInformation.GUStoreID INNER JOIN
                                 a_CustAccountInformation ON I_SerialNumber_Header.SerialNumber = a_CustAccountInformation.SIMID INNER JOIN
                                 m_CODealerMaster ON m_StoresInformation.GULCOID = m_CODealerMaster.GULCOID
        WHERE        (m_CODealerMaster.LCOCode = N'".$lcocode."') AND (a_CustAccountInformation.ServiceStatus IN ('Active', 'InActive')))m
        Where rnbr >".$lastid."";
        return $this->account=$cobj->get($query);
    }

    

    public function get_lcos_accounts_v1($rownumber,$page,$gulcoid,$guuserid,$user){

        $callsp =new stdClass();
        $callsp->name="CSV_GetLCOAccounts";
        $callsp->sql="EXEC CSV_GetLCOAccounts @GULCOID = ?, @Page = ?";
        //echo $callsp->sql;
                    
        $callsp->parameters=array(array($gulcoid,SQLSRV_PARAM_IN),
            array($page,SQLSRV_PARAM_IN));
            
        //var_dump($callsp->parameters);
                        //$sqlUnit = $context->resolve("mssql:excute");
        $results=$this->sqlUnit->process($callsp,"excute")->results;
        //var_dump($results);
        return $results;
        
    }

    public function get_lcos_accounts_vc_v1($vcno){
        require_once("cachequery.php");
        $cobj =new CacheQuery($this->sqlUnit);
        $query["key"] = $vcno;
        $query["object_name"] = "accounts_lco_v1_single";
        //$lastid=$rownumber*$page;
         $query["sql"] = "Select  * 
        From
        (SELECT   case when a_CustAccountInformation.SystemNotes is null then 0 else 1 end SystemNotes ,M_PromotionHeader.guproplanid,a_CustAccountInformation.guaccountid,
        '' enddate,ProPlanCode,0 B_Isvalid,
        '' CAFReserve,   
        a_CustAccountInformation.AccountNo as SMSSID ,
        a_CustAccountInformation.SIMID as VCNO ,
        a_CustAccountInformation.STBID as STBNO , 
        a_CustAccountInformation.ServiceStatus as Status ,
        a_CustAccountInformation.CASType as CASType,
        '-' as RefNo , 
        '-' NextRenewal ,
        m_CODealerMaster.LCOCode,
         m_CustomerInfo.FirstName SubsName ,
         ROW_NUMBER() OVER(ORDER BY a_CustAccountInformation.AccountNo) AS RowNumber,
         a_CustAccountInformation.MainKey,
         a_CustAccountInformation.DateofActivated
         FROM         m_CODealerMaster WITH (NOLOCK) inner join m_StoresInformation WITH (NOLOCK) ON   m_CODealerMaster.GULCOID = m_StoresInformation.GULCOID  inner join I_SerialNumber_Header WITH (NOLOCK) on   m_StoresInformation.GUStoreID = I_SerialNumber_Header.GUStoreID inner join a_CustAccountInformation WITH (NOLOCK) ON I_SerialNumber_Header.SerialNumber = a_CustAccountInformation.SIMID   inner join          m_CustomerInfo WITH (NOLOCK) ON a_CustAccountInformation.CustCode = m_CustomerInfo.CustCode and ServiceStatus <> 'Void' inner join M_PromotionHeader (NOLOCK) on a_CustAccountInformation.GUPromotionID=M_PromotionHeader.GUProPlanID and  1='1' and  a_CustAccountInformation.SIMID ='".$vcno."') m";
        //echo $query["sql"];
        return $this->account=$cobj->get_single($query);
    }

    public function get_lcos_accounts_v1_count($gulcoid,$guuserid,$user){
        require_once("cachequery.php");
        $cobj =new CacheQuery($this->sqlUnit);
        $query["key"] = $gulcoid."-".$guuserid."-".$user;
        $query["object_name"] = "accounts_lco_count_v1";
        
        $query["sql"] = "Select Count(0) as reccount From csv_LCOAccounts Where GULCOID='".$gulcoid."'";
        $rec=$cobj->get($query);
        if(count($rec)>0){
            return $rec[0]->reccount;
        }else{
            return 0;
        }
    }

    public function get_lcos_accounts_count($lcocode){
        require_once("cachequery.php");
        $cobj =new CacheQuery($this->sqlUnit);
        $query["key"] = $lcocode."20x";
        $query["object_name"] = "accounts_lco_count";
        $query["sql"] = "Select Count(0) as reccount From csv_LCOAccounts Where GULCOID='".$lcocode."'";
        $rec=$cobj->get($query);
        if(count($rec)>0){
            return $rec[0]->reccount;
        }else{
            return 0;
        }
    }
}
?>