<?php

return function ($context){
    $request = $context->getRequest();
    //echo $request->Body();
    $body=json_decode($request->Body());
    //var_dump($body);
    //$param["ssss"]="out";
    $callsp =new stdClass();
    $callsp->name="samplesp";
    $callsp->sql="EXEC stp_Create_Item @VALIN = ?, @VALOUT = ?";
    $callsp->parameters=array(array($body->valueIN,SQLSRV_PARAM_IN),
        array($param["ssss"],SQLSRV_PARAM_IN));
    $sqlUnit = $context->resolve("mssql:excute");
    $r =$sqlUnit->process($callsp);
    //var_dump($sqlUnit->process($callsp)->results);
    return $r->results;
}; 