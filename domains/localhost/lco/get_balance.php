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
    //try{
        require_once(TENANT_RESOURCE_PATH."/bo_lib/master/lco_op.php");
        $request = $context->getRequest();
        $lcocode = $request->Params()->lcocode;
        $sqlUnit = $context->resolve("mssql:query");
        $lco =new lco($sqlUnit);
        $lco->get_lcobycode($lcocode);
        return $lco->get_balance();
    //}catch(Exception $e){
        //return $e;
    //}
};