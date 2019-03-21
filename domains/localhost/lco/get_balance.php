<?php

function GetGULCOID($sqlUnit, $lcocode){
    $sql="SELECT GULCOID FROM m_CODealerMaster  WHERE LCOCode = '".$lcocode."'";
    $dbobj= $sqlUnit->process($sql);
    //var_dump($dbobj);
    if(count($dbobj)>0){
        return $dbobj[0]->GULCOID;
    }else{
        throw new Exception('LCO was not found');
    }

}

return function($context){
    $request = $context->getRequest();
    $lcocode = $request->Params()->lcocode;
    $sqlUnit = $context->resolve("mssql:query");
    //echo $lcocode;
    $sql="SELECT Top (1) OpeningBalance,TranDateTime FROM a_LCOLedger  WHERE GULCOID = '". GetGULCOID($sqlUnit, $lcocode)."' ORDER BY ID DESC";
    
    $dbobj= $sqlUnit->process($sql);
    $sqlUnit->Close();
    if(count($dbobj)>0){
        return $dbobj[0]->OpeningBalance;
    }else{
        return 0;
    }
};