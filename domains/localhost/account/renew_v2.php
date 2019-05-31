<?php
return function ($context){
    $request = $context->getRequest();
    //echo $request->Body();
    $body=json_decode($request->Body());
    //try{
        require_once(TENANT_RESOURCE_PATH."/bo_lib/master/account.php");
        require_once(TENANT_RESOURCE_PATH."/bo_lib/master/lco_op.php");
        require_once(TENANT_RESOURCE_PATH."/bo_lib/master/accountrenew_v2.php");
        require_once(TENANT_RESOURCE_PATH."/bo_lib/master/accountrecharge_v2.php");
        require_once(TENANT_RESOURCE_PATH."/bo_lib/master/acc_reconnect.php");
        /*$redis= $context->resolve("redis:get");
        $key="renewcalculator-".$request->Params()->vcno;
        $cache=$redis->get($key);
        if(isset($cache)){
            return json_decode($cache);
        }*/
        $sqlUnit = $context->resolve("mssql:query");
        $Account =new Account($sqlUnit);
        $lco =new lco($sqlUnit);
        $Accountobject=$lco->get_lcos_accounts_vc_v1($request->Params()->vcno);
        //var_dump("");
        //$AccountRewAmount=
        if(isset($Accountobject)){
            if(strtolower($Accountobject->Status)=="active"){
                $Accountobject->entitlements=$Account->get_entitlements($Accountobject->guaccountid);
                $sqlUnit = $context->resolve("mssql:excute");
                //var_dump("");
                $renew =new AccountRenewOp($sqlUnit);
                $obj= $renew->CalacuteRenewPrices($Accountobject,$request->Params()->packagetype);
                //$obj->log= DavvagApiManager::$logObject;
                return $obj;//$renew->CalacuteRenewPrices($Accountobject,$request->Params()->packagetype);
            }else{
                //var_dump("");
                $reconnect=new Reconnect($sqlUnit);
                $Accountobject->entitlements=$reconnect->get_entitlements($Accountobject->guaccountid);
                $sqlUnit = $context->resolve("mssql:excute");
                $renew =new AccountRecahargeOp($sqlUnit);
                $obj= $renew->CalacuteRenewPrices($Accountobject,$request->Params()->packagetype);
                //$obj->log= DavvagApiManager::$logObject;
                return $obj;
            }
        }else{
            throw new Exception('Account not found for the provided vcno.'); 
        }
       
}; 