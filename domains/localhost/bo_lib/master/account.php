<?php
require_once("cachequery.php");
Class Account{
    private $sqlUnit;
    private $account;
    private $entitlements;
    private $channels;
    function __construct($sqlCon){
        $this->sqlUnit=$sqlCon;
    }

    public function get_accountbyvc($vcno){
        $cobj =new CacheQuery($this->sqlUnit);
        $query["key"] = $vcno;
        $query["object_name"] = "accounts";
        $query["sql"] = "select * from a_CustAccountInformation where simid='".$vcno."' and servicestatus<>'void'";
        $this->account=$cobj->get_single($query);
        if(isset($this->account)){
            return $this->account;
        }else{
            throw new Exception('Account not Found to the provided vc number.');
        }
        //$query[]
    }

    public function get_accountbyno($accno){
        $cobj =new CacheQuery($this->sqlUnit);
        $query["key"] = $accno;
        $query["object_name"] = "accounts";
        $query["sql"] = "select * from a_CustAccountInformation where accountno='".$accno."'";
        $this->account=$cobj->get_single($query);

        if(isset($this->account)){
            return $this->account;
        }else{
            throw new Exception('Account not Found to the provided account no.');
        }
        //$query[]
    }

    public function get_accountbyguid($guid){
        $cobj =new CacheQuery($this->sqlUnit);
        $query["key"] = $guid;
        $query["object_name"] = "accounts";
        $query["sql"] = "select * from a_CustAccountInformation where GUAccountID='".$guid."'";
        $this->account=$cobj->get_single($query);
        if(isset($this->account)){
            return $this->account;
        }else{
            throw new Exception('Account not Found to the provided guid.');
        }
        //$query[]
    }

    public function get_entitlements($guaccid=null){
        if(!isset($guaccid)){
            if(!isset($this->account)){
                throw new Exception('Account not queried for this operation contact api vender.');
            }else{
                $guaccid=$this->account->GUAccountID;
            }
        }
        $this->entitlements=array();
        $cobj =new CacheQuery($this->sqlUnit);
        $query["key"] = $guaccid;
        $query["object_name"] = "accounts_entitlements_1";
        $query["sql"] = "SELECT        case when M_NewPackageHeader.packagecode like '%BST%' then 1 else 2 end ALCOrder,case when M_NewPackageHeader.IsAddon =0 then '10' else M_NewPackageHeader.IsAddon end IsAddon1,M_NewPackageHeader.GUPackageID, M_NewPackageHeader.PackageCode, M_NewPackageHeader.PackageDescription, M_NewPackageHeader.PackageCategory, M_NewPackageHeader.Status, 
        M_NewPackageHeader.PkgPrintDiscription, M_NewPackageHeader.International, M_NewPackageHeader.SalesCommAmount, M_NewPackageHeader.SalesCommPerentage, M_NewPackageHeader.AllowPkgChangeforall, 
        M_NewPackageHeader.PackageType, M_NewPackageHeader.Category, M_NewPackageHeader.NCF, M_NewPackageHeader.MRP, M_NewPackageHeader.DRP, M_NewPackageHeader.BroadcasterShare, 
        M_NewPackageHeader.ShareValue, M_NewPackageHeader.IsAddon, M_NewPackageHeader.LCOShare, M_NewPackageHeader.ShareValueLCO,m_SubscriptionServiceDetails.ExpDate,m_SubscriptionServiceDetails.ActivationDate,m_SubscriptionServiceDetails.SubscriptionDate,
        (select  sum(a.CCount) from (
	 select  
	 case 
	 when C.ChannelType = 'SD' then isnull(COUNT(P.ChannelCode),0) 
	 when C.ChannelType = 'HD' then isnull(COUNT(P.ChannelCode),0)*1
	 when C.ChannelType = '4K' then isnull(COUNT(P.ChannelCode),0)*1
	 END  CCount
	  from m_PackageChannelInformation P INNER JOIN E_ChannelMaster C   
	 (nolock) ON  P.ChannelCode = C.ChannelCode 
	 where GUPackageID = M_NewPackageHeader.GUPackageID And C.NCF=1
	 group by C.ChannelType) a) As ChannelCount,
     isnull((select NCFDiscount from Prepaid_NCFDiscount where NCFDiscount=100 and GUPackageID=M_NewPackageHeader.GUPackageID),0) as NCFDiscount
        FROM            m_SubscriptionServiceDetails INNER JOIN
        M_NewPackageHeader ON m_SubscriptionServiceDetails.GUPackageID = M_NewPackageHeader.GUPackageID RIGHT OUTER JOIN
        a_CustAccountInformation ON m_SubscriptionServiceDetails.GUAccountID = a_CustAccountInformation.GUAccountID
        WHERE        (a_CustAccountInformation.GUAccountID = N'".$guaccid."') AND (m_SubscriptionServiceDetails.Status = 'active') AND (m_SubscriptionServiceDetails.AdditionalChannelPackage='Y') order by ALCOrder,IsAddon1 ASC";
        //echo $query["sql"];
        $alacarte1=$cobj->get($query);
            
        
        
        $query["object_name"] = "accounts_base_entitlements";
        $query["sql"] = "SELECT        M_NewPackageHeader.GUPackageID, M_NewPackageHeader.PackageCode, M_NewPackageHeader.PackageDescription, M_NewPackageHeader.PackageCategory, M_NewPackageHeader.Status, 
        M_NewPackageHeader.PkgPrintDiscription, M_NewPackageHeader.International, M_NewPackageHeader.SalesCommAmount, M_NewPackageHeader.SalesCommPerentage, M_NewPackageHeader.AllowPkgChangeforall, 
        M_NewPackageHeader.PackageType, M_NewPackageHeader.Category, M_NewPackageHeader.NCF, M_NewPackageHeader.MRP, M_NewPackageHeader.DRP, M_NewPackageHeader.BroadcasterShare, 
        M_NewPackageHeader.ShareValue, M_NewPackageHeader.IsAddon, M_NewPackageHeader.LCOShare, M_NewPackageHeader.ShareValueLCO, (Select MAx(ScheduleDate) From CAS_SubscriberAmendments Where  VCNo =a_CustAccountInformation.SIMID  and AmendmentType in('Suspend Subscriber','Cancel Service') and Status='P')  AS ExpDate,getdate() AS ActivationDate,getdate() AS SubscriptionDate,
        (select  sum(a.CCount) from (
	 select  
	 case 
	 when C.ChannelType = 'SD' then isnull(COUNT(P.ChannelCode),0) 
	 when C.ChannelType = 'HD' then isnull(COUNT(P.ChannelCode),0)*1
	 when C.ChannelType = '4K' then isnull(COUNT(P.ChannelCode),0)*1
	 END  CCount
	  from m_PackageChannelInformation P INNER JOIN E_ChannelMaster C   
	 (nolock) ON  P.ChannelCode = C.ChannelCode 
	 where GUPackageID = M_NewPackageHeader.GUPackageID And C.NCF=1
	 group by C.ChannelType) a) As ChannelCount,
     isnull((select NCFDiscount from Prepaid_NCFDiscount where NCFDiscount=100 and GUPackageID=M_NewPackageHeader.GUPackageID),0) as NCFDiscount
        FROM            a_CustAccountInformation LEFT OUTER JOIN
        m_SubscriptionInfomation INNER JOIN
        M_NewPackageHeader ON m_SubscriptionInfomation.GUPackageID = M_NewPackageHeader.GUPackageID ON a_CustAccountInformation.GUAccountID = m_SubscriptionInfomation.GUAccountID
        WHERE        (a_CustAccountInformation.GUAccountID = N'".$guaccid."')";
        //echo $query["sql"];
        //echo $query["sql"];
        $baseEn=$cobj->get($query);
        //$this->entitlements=$cobj->get($query);
        //var_dump($baseEn);
        $ChannelCount=0;
        if(isset($baseEn) && count($baseEn)>0){
            $baseEn[0]->CumulativeChannelCount=$ChannelCount;
            $ChannelCount=$baseEn[0]->ChannelCount;
            array_push($this->entitlements,$baseEn[0]);
            
        }
        if(isset($alacarte1) && count($alacarte1)>0){
            foreach ($alacarte1 as $key => $value) {
                # code...
                $value->CumulativeChannelCount=$ChannelCount;
                $ChannelCount+=$value->ChannelCount;
                array_push($this->entitlements,$value);

            }
        }

        
        //var_dump($this->entitlements);
        return $this->entitlements;
    }

    public function get_channels(){
        if(!isset($this->account)){
            throw new Exception('Account not queried for this operation contact api vender.');
        }
        $cobj =new CacheQuery($this->sqlUnit);
        $query["key"] = $this->account->GUAccountID;
        $query["object_name"] = "accounts_channels";
        $query["sql"] = "SELECT        m_PackageChannelInformation.ChannelCode, m_PackageChannelInformation.ChannelName, m_SubscriptionServiceDetails.ExpDate, m_SubscriptionServiceDetails.BillingStatus, 
        m_SubscriptionServiceDetails.SubscriptionDate, m_SubscriptionServiceDetails.Status AS EntitlementStatus, a_CustAccountInformation.ServiceStatus, m_PackageChannelInformation.GUPackageID
FROM            m_PackageChannelInformation INNER JOIN
        m_SubscriptionServiceDetails ON m_PackageChannelInformation.GUPackageID = m_SubscriptionServiceDetails.GUPackageID RIGHT OUTER JOIN
        a_CustAccountInformation ON m_SubscriptionServiceDetails.GUAccountID = a_CustAccountInformation.GUAccountID
        WHERE        (a_CustAccountInformation.GUAccountID = N'".$this->account->GUAccountID."') AND (m_SubscriptionServiceDetails.Status IN ('active', 'new'))";

        //echo $query["sql"];
        $this->channels=$cobj->get($query);
        $query["object_name"] = "accounts_basechannels";
        $query["sql"]= "SELECT        m_PackageChannelInformation.ChannelCode, m_PackageChannelInformation.ChannelName, CAS_SubscriberAmendments.ScheduleDate AS ExpDate, a_CustAccountInformation.BillingStatus, 
        a_CustAccountInformation.DateofActivated AS SubscriptionDate, a_CustAccountInformation.ServiceStatus AS EntitlementStatus, a_CustAccountInformation.ServiceStatus, m_PackageChannelInformation.GUPackageID
FROM            m_PackageChannelInformation INNER JOIN
        m_SubscriptionInfomation ON m_PackageChannelInformation.GUPackageID = m_SubscriptionInfomation.GUPackageID INNER JOIN
        CAS_SubscriberAmendments ON m_SubscriptionInfomation.GUPackageID = CAS_SubscriberAmendments.GUPackageID INNER JOIN
        a_CustAccountInformation ON m_SubscriptionInfomation.GUAccountID = a_CustAccountInformation.GUAccountID AND CAS_SubscriberAmendments.AccountNo = a_CustAccountInformation.AccountNo
        WHERE        (a_CustAccountInformation.GUAccountID = N'".$this->account->GUAccountID."')  AND (CAS_SubscriberAmendments.Status = N'p') AND (CAS_SubscriberAmendments.ScheduleDate > GETDATE())";
        //echo $query["sql"];
        $basechannels=$cobj->get($query);
        array_push($this->channels,$basechannels);
        return $this->channels;
    }

    



}
?>