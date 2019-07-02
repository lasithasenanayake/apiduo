<?php
        /**
         * Developer :Lasitha Senanayake
         * Date : May 10 2019
         * Comments: Get Renew Calculation v1
         * email :lasitha.senanayake@gmail.com
         * github : https://github.com/lasithasenanayake
         * company: Duo Software  
         */
return function ($context){
    $request = $context->getRequest();
    //echo TENANT_RESOURCE_PATH."/bo_lib/master/file_csv_q.php";
    require_once(TENANT_RESOURCE_PATH."/bo_lib/master/file_csv_q.php");
    if($request->Params()->lcoid=="appx003-090-".ENTITY){
        $l=new LCO_Pending();
        return $l->getLCO();
    }
}; 