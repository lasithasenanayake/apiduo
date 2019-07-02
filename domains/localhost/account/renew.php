<?php
        /**
         * Developer :Lasitha Senanayake
         * Date : May 9 2019
         * Comments: Get Renew Calculation v0
         * email :lasitha.senanayake@gmail.com
         * github : https://github.com/lasithasenanayake
         * company: Duo Software  
         */
return function ($context){
    $request = $context->getRequest();
    //echo $request->Body();
    $body=json_decode($request->Body());
    //try{
        require_once(TENANT_RESOURCE_PATH."/bo_lib/master/account.php");
        require_once(TENANT_RESOURCE_PATH."/bo_lib/master/accountrenew.php");
        $sqlUnit = $context->resolve("mssql:query");
        $Account =new Account($sqlUnit);
        $Accountobject=$Account->get_accountbyvc($request->Params()->vcno);
        $Accountobject->entitlements=$Account->get_entitlements();
        $sqlUnit = $context->resolve("mssql:excute");
        $renew =new AccountRenewOp($sqlUnit);
        return $renew->CalacuteRenewPrices($Accountobject,$request->Params()->packagetype);
       
}; 