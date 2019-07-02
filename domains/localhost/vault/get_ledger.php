<?php

return function($context){
    $request = $context->getRequest();
    $vaultid = $request->Params()->vaultid;
    $type = $request->Params()->type;
    $page=$request->Params()->page;
    $row=100*(int)$page;
    //return getUser();
    
    $sqlUnit = $context->resolve("mssql:query");
    $sql="Select top 100 * from 
    (select  *,ROW_NUMBER() OVER(ORDER BY ID desc) AS RowNumber 
    from a_LCOLedger  (nolock) where GULCOID='".$vaultid."') ledger
    where ledger.RowNumber >".$row."";
    $dbobj= $sqlUnit->process($sql);
    return $dbobj;
};