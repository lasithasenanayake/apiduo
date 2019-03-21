<?php

return function ($context){
    $request = $context->getRequest();
    //echo $request->Body();
    $body=json_decode($request->Body());
    //var_dump($body);
    $callsp =new stdClass();
    $callsp->name="samplesp";
    $callsp->sql="EXEC stp_Create_Item @VALIN = ?, @VALOUT = ?";
    $callsp->parameters=array(array(&$body->valueIN,SQLSRV_PARAM_IN),
        array(&$body->valueOut,SQLSRV_PARAM_IN));
    $sqlUnit = $context->resolve("mssql:excute");
    return $sqlUnit->process($callsp)->results;
}; 