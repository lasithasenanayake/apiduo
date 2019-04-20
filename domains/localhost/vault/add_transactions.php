<?php
return function($context){
    $request = $context->getRequest();
    $objectBlock= json_decode($request->Body());
    $SecurityVault=DavvagApiManager::$SercurityVault;
    $callsp =new stdClass();
    $callsp->name="A_LCOVaultBlock";
    $callsp->sql="EXEC A_LCOVaultBlock 
    @ApplicationID = ?, 
    @UserID = ?,
    @GULCOID = ?,
    @GUAccountID =?,
    @BlockAmount=?";
    $sqlUnit = $context->resolve("mssql:excute");
    $callsp->parameters=array(
    array($SecurityVault->ApplicationKey,SQLSRV_PARAM_IN),//AccountNo
    array($SecurityVault->UserName,SQLSRV_PARAM_IN),//CreateUser
    array($objectBlock->gulcoid ,SQLSRV_PARAM_IN),//TransID
    array($objectBlock->guaccountid,SQLSRV_PARAM_IN),//VCno
    array($objectBlock->amount,SQLSRV_PARAM_IN));//GetDate
    $results=$sqlUnit->process($callsp)->results;
    $redis = $context->resolve("redis:get");
    $redis->delete("vault-".$objectBlock->gulcoid."-lco");
    return $results;
};
?>