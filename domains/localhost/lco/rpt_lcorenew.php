<?php
return function ($context){
    $request = $context->getRequest();
    //echo $request->Body();
    $body=json_decode($request->Body());
    //try{
        $cacheObj=CacheData::getObjects($request->Params()->lastid."-".$request->Params()->lcocode."-".$request->Params()->days,"exp_box_calculation");
            if($cacheObj){
                return $cacheObj;
            }
        require_once(TENANT_RESOURCE_PATH."/bo_lib/master/account.php");
        require_once(TENANT_RESOURCE_PATH."/bo_lib/master/accountrenew.php");
        require_once(TENANT_RESOURCE_PATH."/bo_lib/master/lco_op.php");
        $sqlUnit = $context->resolve("mssql:query");
        $mainDoc =new stdClass();
        $report=array();
        
        $Account =new Account($sqlUnit);
        $lco =new lco($sqlUnit);
        if($request->Params()->lastid==0){
            //$sql=""
            $mainDoc->record_count=0;
        }
        $results=$lco->get_expiring_vcnos($request->Params()->lastid,$request->Params()->lcocode,$request->Params()->days);
        //var_dump($results);
        foreach ($results as $key => $value) {
            # code...
            $item=CacheData::getObjects($value->VCNo,"billing_calculation_renew");
            if(!$cacheObj){
                $sqlUnit = $context->resolve("mssql:query");
                $Accountobject=$Account->get_accountbyvc($value->VCNo);
                $Accountobject->entitlements=$Account->get_entitlements();
                $sqlUnit = $context->resolve("mssql:excute");
                $renew =new AccountRenewOp($sqlUnit);
            
                $item= $renew->CalacuteRenewPrices($Accountobject,"quick");
                $item->id=$value->ID;
                
                CacheData::setObjects($value->VCNo,"billing_calculation_renew",$item);
            }
            $mainDoc->lastid=$value->ID;
            array_push($report,$item);
        }
        $mainDoc->results=$report;
        if(count($report)>0){
            CacheData::setObjects($request->Params()->lastid."-".$request->Params()->lcocode."-".$request->Params()->days,"exp_box_calculation",$mainDoc);
        }
        return $mainDoc;
       
}; 
