<?php
//private function GetAll($)
function Migra($data){
    try{
        require_once(TENANT_RESOURCE_PATH."/sqlsvr/sqlsvr.php");
        
        
        $sql =new SqlConnector("report");
        $input=new stdClass();
        $input->Table=ENTITY."_csv_LCOAccounts";
        $input->PrimaryColumns=array("guaccountid");
        $input->Values=$data;
        $sql->BulkSend($input);
        return null;
    }catch(Exception $e){
        return $e->getMessage();
    }
}

function MigraPkg($data){
    try{
        require_once(TENANT_RESOURCE_PATH."/sqlsvr/sqlsvr.php");
        
        
        $sql =new SqlConnector("report");
        $input=new stdClass();
        $input->Table=ENTITY."_LCOPricingDetails_1";
        $input->PrimaryColumns=array("guaccountid","VCNO","PackageCode");
        $input->Values=$data;
        $sql->BulkSend($input);
        return null;
    }catch(Exception $e){
        return $e->getMessage();
    }
}

return function ($context){
    $request = $context->getRequest();
    //echo $request->Body();
    $body=json_decode($request->Body());
    //try{
        $page=$request->Params()->page;
        $gulcoid=$request->Params()->gulcoid;
        $cache=$request->Params()->cache;
        $rownumber=50;
        $guuserid="";//$request->Params()->guuserid;
        $user="";//$request->Params()->user;
        $cacheid=$page."-".$gulcoid."-".$rownumber."-".$guuserid."-".$user;
        if($cache!="update"){
            $cacheObj=CacheData::getObjects_fullcache($cacheid,"box_calculation_v1");
            if($cacheObj){
                  return $cacheObj;
            }
        }
    
        require_once(TENANT_RESOURCE_PATH."/bo_lib/master/account.php");
        require_once(TENANT_RESOURCE_PATH."/bo_lib/master/accountrenew_v2.php");
        require_once(TENANT_RESOURCE_PATH."/bo_lib/master/accountrecharge_v2.php");
        require_once(TENANT_RESOURCE_PATH."/bo_lib/master/lco_op.php");
        require_once(TENANT_RESOURCE_PATH."/bo_lib/master/acc_reconnect.php");
        $sqlUnit = $context->resolve("mssql:query");
        $mainDoc =new stdClass();
        $report=array();
        $updateprices=array();
        $packagePrice=array();
        $Account =new Account($sqlUnit);
        $lco =new lco($sqlUnit);
        $results=$lco->get_lcos_accounts_v1($rownumber,$page,$gulcoid,$guuserid,$user);
        
        if($request->Params()->page==0){
            $mainDoc->record_count=$lco->get_lcos_accounts_v1_count($gulcoid,$guuserid,$user);
            $rcount=$mainDoc->record_count;
            
        }
        foreach ($results as $key => $value) {
                if(strtolower($value->Status)=="active"){
                    //return 
                    $item=null;
                    if($cache!="update"){
                        $item=CacheData::getObjects($value->VCNO,"billing_calculation_renew_v1");
                    }
                    if(!$item){
                        $sqlUnit = $context->resolve("mssql:query");
                        $value->entitlements=$Account->get_entitlements($value->guaccountid);
                        $sqlUnit = $context->resolve("mssql:excute");
                        $renew =new AccountRenewOp($sqlUnit);
                        //try
                        $item= $renew->CalacuteRenewPrices($value,"quick");
                        
                        $item->id=$value->RowNumber;
                        CacheData::setObjects($value->VCNO,"billing_calculation_renew_v1",$item);
                        
                        unset($item->entitlements);
                        array_push($report,$item);
                        
                    }else{
                        unset($value->entitlements);
                        array_push($report,$item);
                    }
                }else{
                    //return $value;
                    $item=null;
                    if($cache!="update"){
                        $item=CacheData::getObjects($value->VCNO,"billing_calculation_reconnect_v1");
                    }
                    if(!$item){ 
                        $sqlUnit = $context->resolve("mssql:query");
                        $reconnect=new Reconnect($sqlUnit);
                        $value->entitlements=$reconnect->get_entitlements($value->guaccountid);
                        $sqlUnit = $context->resolve("mssql:excute");
                        $renew =new AccountRecahargeOp($sqlUnit);
                    
                        $item= $renew->CalacuteRenewPrices($value,"quick");
                        $item->id=$value->RowNumber;
                        CacheData::setObjects($value->VCNO,"billing_calculation_reconnect_v1",$item);
                        
                        //array_push($report,$item);
                        unset($item->entitlements);
                        array_push($report,$item);
                    }else{
                        unset($value->reconnection_pkg);
                        array_push($report,$item);
                    }
                }
                $dataitem =new stdClass();
                        $dataitem->SystemNotes =$item->SystemNotes;
                        $dataitem->guproplanid =$item->guproplanid;
                        $dataitem->guaccountid =$item->guaccountid;
                        $dataitem->ExpiryDate =$item->ExpiryDate;
                        $dataitem->ProPlanCode =$item->ProPlanCode;
                        $dataitem->B_Isvalid =$item->B_Isvalid;
                        $dataitem->CAFReserve =$item->CAFReserve;
                        $dataitem->SMSSID =$item->SMSSID;
                        $dataitem->VCNO =$item->VCNO;
                        $dataitem->STBNO =$item->STBNO;
                        $dataitem->Status =$item->Status;
                        $dataitem->CASType =$item->CASType;
                        $dataitem->LCOCode =$item->LCOCode;
                        $dataitem->GULCOID =$item->GULCOID;
                        $dataitem->SubsName =$item->SubsName;
                        $dataitem->RowNumber =$item->RowNumber;
                        $dataitem->MainKey =$item->MainKey;
                        $dataitem->DateofActivated =$item->DateofActivated;
                        $dataitem->TotalB2BAmount =floatval($item->TotalB2BAmount);
                        $dataitem->TotalB2CAmount =floatval($item->TotalB2CAmount);
                        array_push($updateprices,$dataitem);
                        
                        foreach ($item->BillingDetails as $pkey => $pval) {
                            # code...
                            $r =new stdClass();
                            $r->guaccountid =$item->guaccountid;
                            $r->VCNO =$item->VCNO;
                            $r->PackageCode =$pval->PackageCode;
                            //$r->PackageCode =$pval->PackageCode;
                            $r->PackageDescription =$pval->PackageDescription;
                            $r->AmountB2B =floatval($pval->AmountB2B);
                            $r->AmountB2C =floatval($pval->AmountB2C);
                            array_push($packagePrice,$r);
                        }
                        
                $mainDoc->lastid=$value->RowNumber;
            }
        
        
        //var_dump($r);
        $mainDoc->results=$report;
        
        if(count($report)>0){
            CacheData::setObjects($cacheid,"box_calculation_v1",$mainDoc);
        }
        $mainDoc->Error=Migra($updateprices);
        $mainDoc->ErrorDetails=MigraPkg($packagePrice);
        return  $mainDoc;
       
}; 
