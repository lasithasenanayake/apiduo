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
      
        $cobj =new CacheQuery($this->sqlUnit);
        $query["key"] = $guaccid;
        $query["object_name"] = "accounts_entitlements";
        $query["sql"] = "SELECT        M_NewPackageHeader.GUPackageID, M_NewPackageHeader.PackageCode, M_NewPackageHeader.PackageDescription, M_NewPackageHeader.PackageCategory, M_NewPackageHeader.Status, 
        M_NewPackageHeader.PkgPrintDiscription, M_NewPackageHeader.International, M_NewPackageHeader.SalesCommAmount, M_NewPackageHeader.SalesCommPerentage, M_NewPackageHeader.AllowPkgChangeforall, 
        M_NewPackageHeader.PackageType, M_NewPackageHeader.Category, M_NewPackageHeader.NCF, M_NewPackageHeader.MRP, M_NewPackageHeader.DRP, M_NewPackageHeader.BroadcasterShare, 
        M_NewPackageHeader.ShareValue, M_NewPackageHeader.IsAddon, M_NewPackageHeader.LCOShare, M_NewPackageHeader.ShareValueLCO,m_SubscriptionServiceDetails.ExpDate,m_SubscriptionServiceDetails.ActivationDate,m_SubscriptionServiceDetails.SubscriptionDate
        FROM            m_SubscriptionServiceDetails INNER JOIN
        M_NewPackageHeader ON m_SubscriptionServiceDetails.GUPackageID = M_NewPackageHeader.GUPackageID RIGHT OUTER JOIN
        a_CustAccountInformation ON m_SubscriptionServiceDetails.GUAccountID = a_CustAccountInformation.GUAccountID
        WHERE        (a_CustAccountInformation.GUAccountID = N'".$guaccid."') AND (m_SubscriptionServiceDetails.Status IN ('active', 'new'))";
        //echo $query["sql"];
        $this->entitlements=$cobj->get($query);
        if(!isset($this->entitlements)){
            $this->entitlements=array();
        }
        $query["object_name"] = "accounts_base_entitlements";
        $query["sql"] = "SELECT        M_NewPackageHeader.GUPackageID, M_NewPackageHeader.PackageCode, M_NewPackageHeader.PackageDescription, M_NewPackageHeader.PackageCategory, M_NewPackageHeader.Status, 
        M_NewPackageHeader.PkgPrintDiscription, M_NewPackageHeader.International, M_NewPackageHeader.SalesCommAmount, M_NewPackageHeader.SalesCommPerentage, M_NewPackageHeader.AllowPkgChangeforall, 
        M_NewPackageHeader.PackageType, M_NewPackageHeader.Category, M_NewPackageHeader.NCF, M_NewPackageHeader.MRP, M_NewPackageHeader.DRP, M_NewPackageHeader.BroadcasterShare, 
        M_NewPackageHeader.ShareValue, M_NewPackageHeader.IsAddon, M_NewPackageHeader.LCOShare, M_NewPackageHeader.ShareValueLCO, getdate() AS ExpDate,getdate() AS ActivationDate,getdate() AS SubscriptionDate
        FROM            a_CustAccountInformation LEFT OUTER JOIN
        m_SubscriptionInfomation INNER JOIN
        M_NewPackageHeader ON m_SubscriptionInfomation.GUPackageID = M_NewPackageHeader.GUPackageID ON a_CustAccountInformation.GUAccountID = m_SubscriptionInfomation.GUAccountID
        WHERE        (a_CustAccountInformation.GUAccountID = N'".$guaccid."')";
        //echo $query["sql"];
        //echo $query["sql"];
        $baseEn=$cobj->get($query);
        //$this->entitlements=$cobj->get($query);
        //var_dump($baseEn);
        array_push($this->entitlements,$baseEn[0]);
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