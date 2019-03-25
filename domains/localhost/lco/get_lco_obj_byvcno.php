<?php

return function ($context){
    $request = $context->getRequest();
    //echo $request->Body();
    $body=json_decode($request->Body());
    try{
        require_once(TENANT_RESOURCE_PATH."/bo_lib/master/lco_op.php");
        //var_dump($body);
       
        //var_dump($body);
        $sqlUnit = $context->resolve("mssql:query");
        $lco =new lco($sqlUnit);
        $lcoobject=$lco->get_lcobyvcno($request->Params()->vcno);
        return $lcoobject;
    }catch(Exception $e){
        return $e;
    }
}; 