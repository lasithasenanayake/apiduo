<?php
Class AccountRenewOp{
    private $sqlUnit;
    function __construct($sqlCon){
        $this->sqlUnit=$sqlCon;
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
        $MainObj=new stdClass();
        $MainObj->vcno=$Accountobject->SIMID;
        $MainObj->stbno=$Accountobject->STBID;
        $MainObj->accountno=$Accountobject->AccountNo;
        $MainObj->ServiceStatus=$Accountobject->ServiceStatus;
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
                        $callsp =new stdClass();
                        $callsp->name="SP_PrePaid_Renew_BasePack_NewTariff_V3";
                        $callsp->sql="EXEC SP_PrePaid_Renew_BasePack_NewTariff_V3 @AccountNo = ?, @PeriodInMonths = ?,
                        @CreateUser= ?,@TransID= ?,@VCno= ?,
                        @CheckAmount= ?,@QuickRenew= ?,@GUProPlanID= ?,@GUPackageID= ?,@CurrntExpiryDate= ?,@CasType= ?,
                        @GUAccountID= ?,@GetDate=?";
                    
                        $callsp->parameters=array(array($Accountobject->AccountNo,SQLSRV_PARAM_IN),
                        array(1,SQLSRV_PARAM_IN),
                        array("webcall",SQLSRV_PARAM_IN),
                        array(0,SQLSRV_PARAM_IN),
                        array($Accountobject->SIMID,SQLSRV_PARAM_IN),
                        array(1,SQLSRV_PARAM_IN),
                        array($quick,SQLSRV_PARAM_IN),
                        array($Accountobject->GUPromotionID,SQLSRV_PARAM_IN),
                        array($value->GUPackageID,SQLSRV_PARAM_IN),
                        array(date_create($value->ExpDate->date),SQLSRV_PARAM_IN),
                        array($Accountobject->CASType,SQLSRV_PARAM_IN),
                        array($Accountobject->GUAccountID,SQLSRV_PARAM_IN),
                        array(date("m-d-Y H:i:s"),SQLSRV_PARAM_IN));
                        //$sqlUnit = $context->resolve("mssql:excute");
                        $results=$this->sqlUnit->process($callsp)->results;
                        if(isset($results[0]->AmountB2B)){
                            $AmountColumns->AmountB2B=$results[0]->AmountB2B;
                            $AmountColumns->AmountB2C=$results[0]->AmountB2C;
                        }
                        $AmountColumns->Results=$results;
                    }catch(Exception $e){
                        //var_dump($e);
                        $AmountColumns->Results=$e->getMessage();
                    }
                break;
                case 4:
                    try{
                        $callsp =new stdClass();
                        $callsp->name="SP_PrePaid_Renew_Alacarte_NewTariff_V3";
                        $callsp->sql="EXEC SP_PrePaid_Renew_Alacarte_NewTariff_V3 
                        @AccountNo= ?, 
                        @PeriodInMonths= ?,
                        @CreateUser= ?,
                        @TransID= ?,
                        @VCno= ?,
                        @CheckAmount= ?,
                        @CurrntExpiryDate= ?,
                        @GUPackageID= ?,
                        @SubscriptionDate= ?,
                        @GUAccountID= ?,
                        @GetDate=?";
                        $callsp->parameters=array(array(
                        $Accountobject->AccountNo,SQLSRV_PARAM_IN),//AccountNo
                        array(1,SQLSRV_PARAM_IN),//PeriodInMonths
                        array("webcall",SQLSRV_PARAM_IN),//CreateUser
                        array(0,SQLSRV_PARAM_IN),//TransID
                        array($Accountobject->SIMID,SQLSRV_PARAM_IN),//VCno
                        array(1,SQLSRV_PARAM_IN),//CheckAmount
                        array(date_create($value->ExpDate->date),SQLSRV_PARAM_IN),//CurrntExpiryDate
                        array($value->GUPackageID,SQLSRV_PARAM_IN),//GUPackageID
                        array(date_create($value->SubscriptionDate->date),SQLSRV_PARAM_IN),//SubscriptionDate
                        array($Accountobject->GUAccountID,SQLSRV_PARAM_IN),//GUAccountID
                        array(date("m-d-Y H:i:s"),SQLSRV_PARAM_IN));//GetDate
                        //var_dump($callsp->parameters);
                        //$sqlUnit = $context->resolve("mssql:excute");
                        $results=$this->sqlUnit->process($callsp)->results;
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