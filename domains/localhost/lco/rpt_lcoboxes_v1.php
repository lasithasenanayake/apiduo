<?php
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
        require_once(TENANT_RESOURCE_PATH."/bo_lib/master/accountrenew_v1.php");
        require_once(TENANT_RESOURCE_PATH."/bo_lib/master/accountrecharge_v1.php");
        require_once(TENANT_RESOURCE_PATH."/bo_lib/master/lco_op.php");
        require_once(TENANT_RESOURCE_PATH."/bo_lib/master/acc_reconnect.php");
        $sqlUnit = $context->resolve("mssql:query");
        $mainDoc =new stdClass();
        $report=array();
       
        $Account =new Account($sqlUnit);
        $lco =new lco($sqlUnit);
        
        if($request->Params()->page==0){
            //$sql=""
            $mainDoc->record_count=$lco->get_lcos_accounts_v1_count($gulcoid,$guuserid,$user);
            $rcount=$mainDoc->record_count;
        }

        $results=$lco->get_lcos_accounts_v1($rownumber,$page,$gulcoid,$guuserid,$user);
        //$results;
        //var_dump($results);
        foreach ($results as $key => $value) {
                if(strtolower($value->Status)=="active"){
                    //return 
                    $item=null;
                    if($cache!="update"){
                        $item=CacheData::getObjects($value->VCNO,"billing_calculation_renew_v1");
                    }
                    if(!$item){
                        $sqlUnit = $context->resolve("mssql:query");
                        //$Accountobject=$Account->get_accountbyvc($value->VCNO);
                        $value->entitlements=$Account->get_entitlements($value->guaccountid);
                        $sqlUnit = $context->resolve("mssql:excute");
                        $renew =new AccountRenewOp($sqlUnit);
                    
                        $item= $renew->CalacuteRenewPrices($value,"quick");
                        
                        $item->id=$value->RowNumber;
                        //var_dump($value);
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
                        //$Accountobject=$Account->get_accountbyvc($value->VCNO);
                        $value->entitlements=$reconnect->get_entitlements($value->guaccountid);
                       
                        //return $value->reconnection_pkg;
                        $sqlUnit = $context->resolve("mssql:excute");
                        $renew =new AccountRecahargeOp($sqlUnit);
                    
                        $item= $renew->CalacuteRenewPrices($value,"quick");
                        //return $item;
                        $item->id=$value->RowNumber;
                        //var_dump($value);
                        CacheData::setObjects($value->VCNO,"billing_calculation_reconnect_v1",$item);
                        unset($item->entitlements);
                        array_push($report,$item);
                    }else{
                        unset($value->reconnection_pkg);
                        array_push($report,$item);
                    }
                }
                $mainDoc->lastid=$value->RowNumber;
            }
            
            
        
        
        $mainDoc->results=$report;
        if(count($report)>0){
            CacheData::setObjects($cacheid,"box_calculation_v1",$mainDoc);
        }
        return  $mainDoc;
       
}; 
