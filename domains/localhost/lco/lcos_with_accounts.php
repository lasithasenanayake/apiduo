<?php
return function ($context){
    $request = $context->getRequest();
    //echo $request->Body();
    $body=json_decode($request->Body());
        require_once(TENANT_RESOURCE_PATH."/bo_lib/master/lco_op.php");
        //var_dump($body);
       
        //var_dump($body);
        $sqlUnit = $context->resolve("mssql:query");
        $sql="SELECT  LCOCode
        ,GULCOID
        ,AccountCount
        FROM vw_LCOWithAccounts Order By AccountCount";
        return $sqlUnit->process($sql);
    
}; 