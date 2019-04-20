<?php
Class Reconnect{
    private $sqlUnit;
    function __construct($sqlCon){
        $this->sqlUnit=$sqlCon;
    }
    
    public function get_entitlements($guaccid){
        
        $this->entitlements=array();
        $cobj =new CacheQuery($this->sqlUnit);
        $query["key"] = $guaccid;
        $query["object_name"] = "accounts_entitlements_rc";
        $query["sql"] = "Select * From  (SELECT        case when M_NewPackageHeader.PackageCode like '%BST%' then 1 else 2 end ALCOrder,case when M_NewPackageHeader.IsAddon =0 then '10' else M_NewPackageHeader.IsAddon end IsAddon1
        ,M_NewPackageHeader.GUPackageID, M_NewPackageHeader.PackageCode, M_NewPackageHeader.PackageDescription, M_NewPackageHeader.PackageCategory, M_NewPackageHeader.Status, 
        M_NewPackageHeader.PkgPrintDiscription, M_NewPackageHeader.International, M_NewPackageHeader.SalesCommAmount, M_NewPackageHeader.SalesCommPerentage, M_NewPackageHeader.AllowPkgChangeforall, 
        M_NewPackageHeader.PackageType, M_NewPackageHeader.Category, M_NewPackageHeader.NCF, M_NewPackageHeader.MRP, M_NewPackageHeader.DRP, M_NewPackageHeader.BroadcasterShare, 
        M_NewPackageHeader.ShareValue, M_NewPackageHeader.IsAddon, M_NewPackageHeader.LCOShare, M_NewPackageHeader.ShareValueLCO, m_SubscriptionServiceDetails.ExpDate, 
                m_SubscriptionServiceDetails.ActivationDate, m_SubscriptionServiceDetails.SubscriptionDate, E_PackageBouquetMapping.BouquetCode,
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
	 group by C.ChannelType) a) As ChannelCount
        FROM            m_SubscriptionServiceDetails INNER JOIN
                M_NewPackageHeader ON m_SubscriptionServiceDetails.GUPackageID = M_NewPackageHeader.GUPackageID INNER JOIN
                E_PackageBouquetMapping ON M_NewPackageHeader.PackageCode = E_PackageBouquetMapping.PackageCode RIGHT OUTER JOIN
                a_CustAccountInformation ON m_SubscriptionServiceDetails.GUAccountID = a_CustAccountInformation.GUAccountID
        WHERE        (a_CustAccountInformation.GUAccountID = N'".$guaccid."') AND (m_SubscriptionServiceDetails.ExpiryType = 1) AND (m_SubscriptionServiceDetails.FreeAlacarte = 'D') AND (m_SubscriptionServiceDetails.ExpDate < GETDATE())
        UNION
        SELECT        case when M_NewPackageHeader.PackageCode like '%BST%' then 1 else 2 end ALCOrder,
        case when M_NewPackageHeader.IsAddon =0 then '10' else M_NewPackageHeader.IsAddon end IsAddon1,M_NewPackageHeader.GUPackageID, M_NewPackageHeader.PackageCode, M_NewPackageHeader.PackageDescription, M_NewPackageHeader.PackageCategory, M_NewPackageHeader.Status, 
                M_NewPackageHeader.PkgPrintDiscription, M_NewPackageHeader.International, M_NewPackageHeader.SalesCommAmount, M_NewPackageHeader.SalesCommPerentage, M_NewPackageHeader.AllowPkgChangeforall, 
                M_NewPackageHeader.PackageType, M_NewPackageHeader.Category, M_NewPackageHeader.NCF, M_NewPackageHeader.MRP, M_NewPackageHeader.DRP, M_NewPackageHeader.BroadcasterShare, 
                M_NewPackageHeader.ShareValue, M_NewPackageHeader.IsAddon, M_NewPackageHeader.LCOShare, M_NewPackageHeader.ShareValueLCO, m_SubscriptionServiceDetails.ExpDate, 
                m_SubscriptionServiceDetails.ActivationDate, m_SubscriptionServiceDetails.SubscriptionDate, E_PackageBouquetMapping.BouquetCode,
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
	 group by C.ChannelType) a) As ChannelCount
        FROM            m_SubscriptionServiceDetails INNER JOIN
                M_NewPackageHeader ON m_SubscriptionServiceDetails.GUPackageID = M_NewPackageHeader.GUPackageID INNER JOIN
                E_PackageBouquetMapping ON M_NewPackageHeader.PackageCode = E_PackageBouquetMapping.PackageCode RIGHT OUTER JOIN
                a_CustAccountInformation ON m_SubscriptionServiceDetails.GUAccountID = a_CustAccountInformation.GUAccountID
        WHERE        (a_CustAccountInformation.GUAccountID = N'".$guaccid."') AND (m_SubscriptionServiceDetails.ExpiryType = 1) AND (m_SubscriptionServiceDetails.FreeAlacarte = 'R') AND (m_SubscriptionServiceDetails.AdditionalChannelPackage='Y')) a order by a.ALCOrder,a.IsAddon1 ASC";
        //echo $query["sql"];
        //echo $query;
        $alacarte=$cobj->get($query);
        //echo "here i am";
        $query["object_name"] = "accounts_base_entitlements_rc";
        $query["sql"] = "SELECT        M_NewPackageHeader.GUPackageID, M_NewPackageHeader.PackageCode, M_NewPackageHeader.PackageDescription, M_NewPackageHeader.PackageCategory, M_NewPackageHeader.Status, 
        M_NewPackageHeader.PkgPrintDiscription, M_NewPackageHeader.International, M_NewPackageHeader.SalesCommAmount, M_NewPackageHeader.SalesCommPerentage, M_NewPackageHeader.AllowPkgChangeforall, 
        M_NewPackageHeader.PackageType, M_NewPackageHeader.Category, M_NewPackageHeader.NCF, M_NewPackageHeader.MRP, M_NewPackageHeader.DRP, M_NewPackageHeader.BroadcasterShare, 
        M_NewPackageHeader.ShareValue, M_NewPackageHeader.IsAddon, M_NewPackageHeader.LCOShare, M_NewPackageHeader.ShareValueLCO, (Select MAx(ScheduleDate) From CAS_SubscriberAmendments Where  VCNo =a_CustAccountInformation.SIMID  and AmendmentType in('Suspend Subscriber','Cancel Service') and Status='P') AS ExpDate, GETDATE() AS ActivationDate, GETDATE()
                AS SubscriptionDate, E_PackageBouquetMapping.BouquetCode,
        (Select count(0) from m_PackageChannelInformation where GUPackageID=M_NewPackageHeader.GUPackageID) As ChannelCount 
        FROM            E_PackageBouquetMapping INNER JOIN
                m_SubscriptionInfomation INNER JOIN
                M_NewPackageHeader ON m_SubscriptionInfomation.GUPackageID = M_NewPackageHeader.GUPackageID ON E_PackageBouquetMapping.PackageCode = M_NewPackageHeader.PackageCode RIGHT OUTER JOIN
                a_CustAccountInformation ON m_SubscriptionInfomation.GUAccountID = a_CustAccountInformation.GUAccountID
        WHERE        (a_CustAccountInformation.GUAccountID = N'".$guaccid."')";
        //ho ec$query["sql"];
        //echo $query["sql"];
        $baseEn=$cobj->get($query);
        //$this->entitlements=$cobj->get($query);
        //var_dump($baseEn);
        //echo "here i am";
        $ChannelCount=0;
        if(isset($baseEn) && count($baseEn)>0){
            $baseEn[0]->CumulativeChannelCount=$ChannelCount;
            $ChannelCount=$baseEn[0]->ChannelCount;
            array_push($this->entitlements,$baseEn[0]);
            
        }
        if(isset($alacarte) && count($alacarte)>0){
            foreach ($alacarte as $key => $value) {
                # code...
                $value->CumulativeChannelCount=$ChannelCount;
                $ChannelCount+=$value->ChannelCount;
                array_push($this->entitlements,$value);

            }
        }
        ///var_dump($this->entitlements);
        return $this->entitlements;
    }
}
?>