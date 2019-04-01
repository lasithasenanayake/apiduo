<?php
Class Reconnect{
    private $sqlUnit;
    function __construct($sqlCon){
        $this->sqlUnit=$sqlCon;
    }
    
    public function get_entitlements($guaccid){
        
      
        $cobj =new CacheQuery($this->sqlUnit);
        $query["key"] = $guaccid;
        $query["object_name"] = "accounts_entitlements_rc";
        $query["sql"] = "SELECT        M_NewPackageHeader.GUPackageID, M_NewPackageHeader.PackageCode, M_NewPackageHeader.PackageDescription, M_NewPackageHeader.PackageCategory, M_NewPackageHeader.Status, 
        M_NewPackageHeader.PkgPrintDiscription, M_NewPackageHeader.International, M_NewPackageHeader.SalesCommAmount, M_NewPackageHeader.SalesCommPerentage, M_NewPackageHeader.AllowPkgChangeforall, 
        M_NewPackageHeader.PackageType, M_NewPackageHeader.Category, M_NewPackageHeader.NCF, M_NewPackageHeader.MRP, M_NewPackageHeader.DRP, M_NewPackageHeader.BroadcasterShare, 
        M_NewPackageHeader.ShareValue, M_NewPackageHeader.IsAddon, M_NewPackageHeader.LCOShare, M_NewPackageHeader.ShareValueLCO, m_SubscriptionServiceDetails.ExpDate, 
                m_SubscriptionServiceDetails.ActivationDate, m_SubscriptionServiceDetails.SubscriptionDate, E_PackageBouquetMapping.BouquetCode
        FROM            m_SubscriptionServiceDetails INNER JOIN
                M_NewPackageHeader ON m_SubscriptionServiceDetails.GUPackageID = M_NewPackageHeader.GUPackageID INNER JOIN
                E_PackageBouquetMapping ON M_NewPackageHeader.PackageCode = E_PackageBouquetMapping.PackageCode RIGHT OUTER JOIN
                a_CustAccountInformation ON m_SubscriptionServiceDetails.GUAccountID = a_CustAccountInformation.GUAccountID
        WHERE        (a_CustAccountInformation.GUAccountID = N'".$guaccid."') AND (m_SubscriptionServiceDetails.ExpiryType = 1) AND (m_SubscriptionServiceDetails.FreeAlacarte = 'D') AND (m_SubscriptionServiceDetails.ExpDate < GETDATE())
        UNION
        SELECT        M_NewPackageHeader.GUPackageID, M_NewPackageHeader.PackageCode, M_NewPackageHeader.PackageDescription, M_NewPackageHeader.PackageCategory, M_NewPackageHeader.Status, 
                M_NewPackageHeader.PkgPrintDiscription, M_NewPackageHeader.International, M_NewPackageHeader.SalesCommAmount, M_NewPackageHeader.SalesCommPerentage, M_NewPackageHeader.AllowPkgChangeforall, 
                M_NewPackageHeader.PackageType, M_NewPackageHeader.Category, M_NewPackageHeader.NCF, M_NewPackageHeader.MRP, M_NewPackageHeader.DRP, M_NewPackageHeader.BroadcasterShare, 
                M_NewPackageHeader.ShareValue, M_NewPackageHeader.IsAddon, M_NewPackageHeader.LCOShare, M_NewPackageHeader.ShareValueLCO, m_SubscriptionServiceDetails.ExpDate, 
                m_SubscriptionServiceDetails.ActivationDate, m_SubscriptionServiceDetails.SubscriptionDate, E_PackageBouquetMapping.BouquetCode
        FROM            m_SubscriptionServiceDetails INNER JOIN
                M_NewPackageHeader ON m_SubscriptionServiceDetails.GUPackageID = M_NewPackageHeader.GUPackageID INNER JOIN
                E_PackageBouquetMapping ON M_NewPackageHeader.PackageCode = E_PackageBouquetMapping.PackageCode RIGHT OUTER JOIN
                a_CustAccountInformation ON m_SubscriptionServiceDetails.GUAccountID = a_CustAccountInformation.GUAccountID
        WHERE        (a_CustAccountInformation.GUAccountID = N'".$guaccid."') AND (m_SubscriptionServiceDetails.ExpiryType = 1) AND (m_SubscriptionServiceDetails.FreeAlacarte = 'R') ";
        //echo $query["sql"];
        $this->entitlements=$cobj->get($query);
        if(!isset($this->entitlements)){
            $this->entitlements=array();
        }
        $query["object_name"] = "accounts_base_entitlements_rc";
        $query["sql"] = "SELECT        M_NewPackageHeader.GUPackageID, M_NewPackageHeader.PackageCode, M_NewPackageHeader.PackageDescription, M_NewPackageHeader.PackageCategory, M_NewPackageHeader.Status, 
        M_NewPackageHeader.PkgPrintDiscription, M_NewPackageHeader.International, M_NewPackageHeader.SalesCommAmount, M_NewPackageHeader.SalesCommPerentage, M_NewPackageHeader.AllowPkgChangeforall, 
        M_NewPackageHeader.PackageType, M_NewPackageHeader.Category, M_NewPackageHeader.NCF, M_NewPackageHeader.MRP, M_NewPackageHeader.DRP, M_NewPackageHeader.BroadcasterShare, 
        M_NewPackageHeader.ShareValue, M_NewPackageHeader.IsAddon, M_NewPackageHeader.LCOShare, M_NewPackageHeader.ShareValueLCO, GETDATE() AS ExpDate, GETDATE() AS ActivationDate, GETDATE() 
                AS SubscriptionDate, E_PackageBouquetMapping.BouquetCode
        FROM            E_PackageBouquetMapping INNER JOIN
                m_SubscriptionInfomation INNER JOIN
                M_NewPackageHeader ON m_SubscriptionInfomation.GUPackageID = M_NewPackageHeader.GUPackageID ON E_PackageBouquetMapping.PackageCode = M_NewPackageHeader.PackageCode RIGHT OUTER JOIN
                a_CustAccountInformation ON m_SubscriptionInfomation.GUAccountID = a_CustAccountInformation.GUAccountID
        WHERE        (a_CustAccountInformation.GUAccountID = N'".$guaccid."')";
        //echo $query["sql"];
        //echo $query["sql"];
        $baseEn=$cobj->get($query);
        //$this->entitlements=$cobj->get($query);
        //var_dump($baseEn);
        array_push($this->entitlements,$baseEn[0]);
        return $this->entitlements;
    }
}
?>