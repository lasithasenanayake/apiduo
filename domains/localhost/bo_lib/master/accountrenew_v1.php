<?php
Class AccountRenewOp{
    private $sqlUnit;
    function __construct($sqlCon){
        $this->sqlUnit=$sqlCon;
    }

    public function rew_basepack($accountno,$period,$createuser,
    $tranid,$vcno,$checkamout,
    $quickrew,
    $guproplnid,
    $packageid,
    $exiperydate,
    $castype,
    $guaccountid,
    $getdate,
    $ChannelCount,$TotalChannelCount,$NCFFree){
        $callsp =new stdClass();
        $callsp->name="SP_PrePaid_Renew_BasePack_NewTariff_V3";
        $callsp->sql="EXEC SP_PrePaid_Renew_BasePack_NewTariff_V3 @AccountNo = ?, @PeriodInMonths = ?,
        @CreateUser= ?,@TransID= ?,@VCno= ?,
        @CheckAmount= ?,@QuickRenew= ?,@GUProPlanID= ?,@GUPackageID= ?,@CurrntExpiryDate= ?,@CasType= ?,
        @GUAccountID= ?,@GetDate=?,@ChannelCount=?,@TotalChannelCount=?";
                    
        $callsp->parameters=array(array($accountno,SQLSRV_PARAM_IN),
            array($period,SQLSRV_PARAM_IN),
            array($createuser,SQLSRV_PARAM_IN),
            array($tranid,SQLSRV_PARAM_IN),
            array($vcno,SQLSRV_PARAM_IN),
            array($checkamout,SQLSRV_PARAM_IN),
            array($quickrew,SQLSRV_PARAM_IN),
            array($guproplnid,SQLSRV_PARAM_IN),
            array($packageid,SQLSRV_PARAM_IN),
            array($exiperydate,SQLSRV_PARAM_IN),
            array($castype,SQLSRV_PARAM_IN),
            array($guaccountid,SQLSRV_PARAM_IN),
            array($getdate,SQLSRV_PARAM_IN),
            array($ChannelCount,SQLSRV_PARAM_IN),
            array($TotalChannelCount,SQLSRV_PARAM_IN));
                        //$sqlUnit = $context->resolve("mssql:excute");
            $results=$this->sqlUnit->process($callsp)->results;
                        
          return $results;
    }

    public function rew_alacarte($accountno,
    $period,
    $createuser,
    $tranid,
    $vcno,
    $checkamount,
    $currentexpiry,
    $packagid,
    $subdate,
    $guaccountid,
    $getdate,
    $ChannelCount,
    $TotalChannelCount,$NCFFree){
        /*@AccountNo nvarchar(50), @PeriodInMonths int, @CreateUser nvarchar(50), 
        @TransID int, @VCno nvarchar(50), @CheckAmount bit, @CurrntExpiryDate datetime, @GUPackageID nvarchar(100), 
        @SubscriptionDate datetime, @GUAccountID nvarchar(50), @GetDate Datetime*/
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
                        @GetDate=?,
                        @ChannelCount=?,
                        @TotalChannelCount=?";
                        $callsp->parameters=array(
                        array($accountno,SQLSRV_PARAM_IN),//AccountNo
                        array($period,SQLSRV_PARAM_IN),//PeriodInMonths
                        array($createuser,SQLSRV_PARAM_IN),//CreateUser
                        array($tranid,SQLSRV_PARAM_IN),//TransID
                        array($vcno,SQLSRV_PARAM_IN),//VCno
                        array($checkamount,SQLSRV_PARAM_IN),//CheckAmount
                        array($currentexpiry,SQLSRV_PARAM_IN),//CurrntExpiryDate
                        array($packagid,SQLSRV_PARAM_IN),//GUPackageID
                        array($subdate,SQLSRV_PARAM_IN),//SubscriptionDate
                        array($guaccountid,SQLSRV_PARAM_IN),//GUAccountID
                        array($getdate,SQLSRV_PARAM_IN),//GUAccountID
                        array($ChannelCount,SQLSRV_PARAM_IN),//GUAccountID
                        array($TotalChannelCount,SQLSRV_PARAM_IN));//GetDate
                        //var_dump($callsp->parameters);
                        //$sqlUnit = $context->resolve("mssql:excute");
                        
                        return $this->sqlUnit->process($callsp)->results;
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
        $NCC=0;
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
                        $value->NCC=$NCC;
                        $results= $this->rew_basepack($Accountobject->SMSSID,1,"webuser",
                                                    0,$Accountobject->VCNO,
                                                    1,$quick,
                                                    $Accountobject->guproplanid,
                                                    $value->GUPackageID,
                                                    date_create($value->ExpDate->date),
                                                    $Accountobject->CASType,
                                                    $Accountobject->guaccountid,date("m-d-Y H:i:s"),$value->ChannelCount,$value->CumulativeChannelCount,$NCC);
                        if(isset($results[0]->AmountB2B)){
                            $AmountColumns->AmountB2B=$results[0]->AmountB2B;
                            $AmountColumns->AmountB2C=$results[0]->AmountB2C;
                        }
                        if(isset($value->ExpDate)){
                            $Accountobject->ExpiryDate=date_create($value->ExpDate->date);
                        }
                        $AmountColumns->Results=$results;
                    }catch(Exception $e){
                        $AmountColumns->Results=$e->getMessage();
                    }
                break;
                case 4:
                    try{
                        $value->NCC=$NCC;
                        $subcriptiondate=date("m-d-Y H:i:s");
                        $expdate=date("m-d-Y H:i:s");
                        if(isset($value->SubscriptionDate->date) ){
                            $subcriptiondate=date_create($value->SubscriptionDate->date);
                            
                        }

                        if(isset($value->ExpDate->date) ){
                            $expdate=date_create($value->ExpDate->date);
                            
                        }
                        $results=$this->rew_alacarte($Accountobject->SMSSID,1,"webuser",0,$Accountobject->VCNO,1,$expdate,
                        $value->GUPackageID,$subcriptiondate,$Accountobject->guaccountid,date("m-d-Y H:i:s")
                        ,$value->ChannelCount,$value->CumulativeChannelCount,$NCC);
                        //var_dump($value->IsAddon);  
                        if(isset($results[0]->AmountB2B)){
                            $AmountColumns->AmountB2B=$results[0]->AmountB2B;
                            $AmountColumns->AmountB2C=$results[0]->AmountB2C;
                        }
                        $AmountColumns->Results=$results; 
                        $diccount=floatval($value->NCFDiscount);
                        if(isset($value->IsAddon) || $value->IsAddon==1){
                            if(strpos(strtolower($value->PackageCode),"bst")===false){
                                if($diccount==100){
                                    $NCC=1;
                                }
                            }else{
                                //echo $value->PackageCode;
                            }
                        }
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
        $MainObj->TotalB2BAmount=number_format((float)$MainObj->TotalB2BAmount, 2, '.', '');
        $MainObj->TotalB2CAmount=number_format((float)$MainObj->TotalB2CAmount, 2, '.', '');
        $MainObj->BillingDetails=$billing;
        return $MainObj;
    }
    
}