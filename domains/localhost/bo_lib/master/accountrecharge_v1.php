<?php
Class AccountRecahargeOp{
    private $sqlUnit;
    function __construct($sqlCon){
        $this->sqlUnit=$sqlCon;
    }

    public function rew_basepack($GUAccountID,$AccountNo,$ActivatedDate,
    $VCNo,$STBNo ,$MainKey,
    $ServiceCode,
    $GUPackageID,
    $CASType,
    $GUProPlanID,
    $PeriodInMonth,
    $User,
    $GetDate,
    $CheckAmount ){
        $callsp =new stdClass();
        $callsp->name="SP_PrePaid_Recharge_BasePack_NewTariff_V3";
        $callsp->sql="EXEC SP_PrePaid_Recharge_BasePack_NewTariff_V3 @GUAccountID = ?, @AccountNo = ?,
        @ActivatedDate = ?, @VCNo = ?, @STBNo = ?,
        @MainKey = ?,@ServiceCode = ?, @GUPackageID = ?,@CASType = ?,@GUProPlanID = ?,@PeriodInMonth = ?,
        @User = ?, @GetDate =?, @CheckAmount = ? ";
        //echo $callsp->sql;
                    
        $callsp->parameters=array(array($GUAccountID,SQLSRV_PARAM_IN),
            array($AccountNo,SQLSRV_PARAM_IN),
            array($ActivatedDate,SQLSRV_PARAM_IN),
            array($VCNo,SQLSRV_PARAM_IN),
            array($STBNo,SQLSRV_PARAM_IN),
            array($MainKey,SQLSRV_PARAM_IN),
            array($ServiceCode,SQLSRV_PARAM_IN),
            array($GUPackageID,SQLSRV_PARAM_IN),
            array($CASType,SQLSRV_PARAM_IN),
            array($GUProPlanID,SQLSRV_PARAM_IN),
            array($PeriodInMonth,SQLSRV_PARAM_IN),
            array($User,SQLSRV_PARAM_IN),
            array($GetDate,SQLSRV_PARAM_IN),
            array($CheckAmount,SQLSRV_PARAM_IN));
            
            //var_dump($callsp->parameters);
                        //$sqlUnit = $context->resolve("mssql:excute");
            $results=$this->sqlUnit->process($callsp)->results;
                        
          return $results;
    }

    public function rew_alacarte($GUPackageID,
    $GUAccountID,
    $AccountNo,
    $VCNo,
    $STBNo,
    $MainKey,
    $ServiceCode ,
    $CASType,
    $SubscriptionDate,
    $GUProPlanID ,
    $REPUID ,
    $PackageCode  ,
    $AlaExpDate  ,
    $PeriodInMonth ,
    $User ,
    $GetDate,
    $CheckAmount){
        $callsp =new stdClass();
                        $callsp->name="SP_PrePaid_Recharge_Alacarte_NewTariff_SingleAla_V3";
                        $callsp->sql="EXEC SP_PrePaid_Recharge_Alacarte_NewTariff_SingleAla_V3 
                        @GUPackageID = ?, 
                        @GUAccountID = ?,
                        @AccountNo = ?,
                        @VCNo = ?,
                        @STBNo = ?,
                        @MainKey = ?,
                        @ServiceCode = ?,
                        @CASType = ?,
                        @GUProPlanID= ?,
                        @SubscriptionDate= ?,
                        @REPUID =?,
                        @PackageCode  =?,
                        @AlaExpDate  =?,
                        @PeriodInMonth =?,
                        @User =?,
                        @GetDate =?,
                        @CheckAmount =?";
                        $callsp->parameters=array(
                        array($GUPackageID,SQLSRV_PARAM_IN),//AccountNo
                        array($GUAccountID,SQLSRV_PARAM_IN),//PeriodInMonths
                        array($VCNo,SQLSRV_PARAM_IN),//CreateUser
                        array($STBNo ,SQLSRV_PARAM_IN),//TransID
                        array($MainKey,SQLSRV_PARAM_IN),//VCno
                        array($ServiceCode,SQLSRV_PARAM_IN),//CheckAmount
                        array($CASType,SQLSRV_PARAM_IN),//CurrntExpiryDate
                        array($GUProPlanID,SQLSRV_PARAM_IN),//GUPackageID
                        array($SubscriptionDate,SQLSRV_PARAM_IN),//SubscriptionDate
                        array($REPUID,SQLSRV_PARAM_IN),//GUAccountID
                        array($PackageCode,SQLSRV_PARAM_IN),//GUAccountID
                        array($AlaExpDate,SQLSRV_PARAM_IN),//GUAccountID
                        array($PeriodInMonth,SQLSRV_PARAM_IN),//GUAccountID
                        array($User ,SQLSRV_PARAM_IN),//GUAccountID
                        array($GetDate,SQLSRV_PARAM_IN),//GUAccountID
                        array($CheckAmount,SQLSRV_PARAM_IN));//GetDate
                        //var_dump($callsp->parameters);
                        //$sqlUnit = $context->resolve("mssql:excute");
                        $results=$this->sqlUnit->process($callsp)->results;
                        return $results;
    }

    public function CalacuteRenewPrices($Accountobject,$packagetype){
        $packageid=0;
        $quick=0;
        switch($packagetype){
            case "base_package":
                $packageid=3;
                break;
            case "alacarte_package":
                $packageid=4;
                break;
            case "quick":
                $packageid=-1;
                break;
            default:
                $packageid=0;
                break;

        }
        
        $billing =array();

        $results=[];
        $MainObj=$Accountobject;
        //$MainObj->vcno=$Accountobject->SIMID;
        //$MainObj->stbno=$Accountobject->STBID;
        //$MainObj->accountno=$Accountobject->AccountNo;
        //$MainObj->ServiceStatus=$Accountobject->ServiceStatus;
        $MainObj->TotalB2BAmount=0;
        $MainObj->TotalB2CAmount=0;
        //return $Accountobject;
        foreach ($Accountobject->entitlements as $key => $value) {
            //var_dump($value->PackageCategory);
            if($packageid!=$value->PackageCategory){
                //echo "im in".$value->PackageCategory." - ".$packageid  ;
                if($packageid!=-1){
                    continue;
                }
            }
            
            $AmountColumns=new stdClass();
            $AmountColumns->AmountB2B=0;
            $AmountColumns->AmountB2C=0;
            //echo $value->PackageCategory;
            switch($value->PackageCategory){
                case 3:
                    //echo "base";
                    try{
                        
                        $results= $this->rew_basepack($Accountobject->guaccountid,$Accountobject->SMSSID,date_create($Accountobject->DateofActivated->date),
                        $Accountobject->VCNO,$Accountobject->STBNO ,$Accountobject->MainKey,
                        $value->BouquetCode,
                        $value->GUPackageID,
                        $Accountobject->CASType,
                        $Accountobject->guproplanid,
                        1,
                        "webuser",
                        date("m-d-Y H:i:s"),
                        1);
                       
                        if(isset($results[0]->AmountB2B)){
                            $AmountColumns->AmountB2B=$results[0]->AmountB2B;
                            $AmountColumns->AmountB2C=$results[0]->AmountB2C;
                        }
                        $AmountColumns->Results=$results;
                    }catch(Exception $e){
                        $AmountColumns->Results=$e->getMessage();
                    }
                break;
                case 4:
                    try{
                        
                        $results=$this->rew_alacarte($value->GUPackageID,
                        $Accountobject->guaccountid,
                        $Accountobject->SMSSID,
                        $Accountobject->VCNO,
                        $Accountobject->STBNO,
                        $Accountobject->MainKey,
                        $value->BouquetCode ,
                        $Accountobject->CASType,
                        date_create($value->SubscriptionDate->date),
                        $Accountobject->guproplanid ,
                        0 ,
                        $value->PackageCode  ,
                        date_create($value->ExpDate->date),
                        1,
                        "user" ,
                        date("m-d-Y H:i:s"),
                        1);
                        if(isset($results[0]->AmountB2B)){
                            $AmountColumns->AmountB2B=$results[0]->AmountB2B;
                            $AmountColumns->AmountB2C=$results[0]->AmountB2C;
                        }
                        
                        $AmountColumns->Results=$results;
                    }catch(Exception $e){
                        $AmountColumns->Results=$e->getMessage();
                    }
                break;
            }
            $itemValue=new stdClass();
                $itemValue->PackageCode=$value->PackageCode;
                $itemValue->PackageDescription=$value->PackageDescription;
                //$itemValue->StartDate= date_create($value->ExpDate->date);
                //$itemValue->EndDate= date_create($value->ExpDate->date)->add(new DateInterval('P1M'));
                $itemValue->PackageCategory=$value->PackageCategory;
                $itemValue->AmountB2B= $AmountColumns->AmountB2B;
                $itemValue->AmountB2C= $AmountColumns->AmountB2C;
                $itemValue->Results= $AmountColumns->Results;
                array_push($billing,$itemValue);
                $MainObj->TotalB2BAmount+=$AmountColumns->AmountB2B;
                $MainObj->TotalB2CAmount+=$AmountColumns->AmountB2C;
        }
        $MainObj->BillingDetails=$billing;
        return $MainObj;
    }
    
}