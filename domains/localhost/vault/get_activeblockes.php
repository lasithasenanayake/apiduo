<?php

return function($context){
    $request = $context->getRequest();
    $vaultid = $request->Params()->vaultid;
    $type = $request->Params()->type;
    //$page=$request->Params()->page;
    //$row=100*(int)$page;
    //return getUser();
    
    $sqlUnit = $context->resolve("mssql:query");
    $sql="Select * from 
    A_CODealerVaultActiveBlocks where GULCOID='".$vaultid."'";
    $dbobj= $sqlUnit->process($sql);
    return $dbobj;
};