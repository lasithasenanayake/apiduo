<?php

return function ($context){
    $request = $context->getRequest();
    //echo $request->Body();
    $body=json_decode($request->Body());
    try{
        require_once(TENANT_RESOURCE_PATH."/bo_lib/master/lco_op.php");
        //var_dump($body);
        $callsp =new stdClass();
        $callsp->name="SP_PrePaid_Pricing_Initial_NewTariff_ForBoth_test";
        $callsp->sql="EXEC SP_PrePaid_Pricing_Initial_NewTariff_ForBoth_test @VCno = ?, @PacakageID = ?,
            @GUProPlanID= ?,@PeriodInMonths= ?,@IsAlacarte= ?,
            @CurrentExpiryDate= ?";
        //var_dump($body);
        $sqlUnit = $context->resolve("mssql:query");
        $lco =new lco($sqlUnit);
        $lcoobject=$lco->get_lcobyvcno($body->VCno);
        $pram["myvalue"]="dddd";
        
        $callsp->parameters=array(array(&$body->AccountNo,SQLSRV_PARAM_IN),
            array(&$body->VCno,SQLSRV_PARAM_IN),
            array(&$body->PacakageID ,SQLSRV_PARAM_IN),
            array(&$body->GUProPlanID,SQLSRV_PARAM_IN),
            array(&$body->PeriodInMonths,SQLSRV_PARAM_IN),
            array(&$body->IsAlacarte,SQLSRV_PARAM_IN),
            array(&$body->CurrentExpiryDate,SQLSRV_PARAM_IN));
        $sqlUnit = $context->resolve("mssql:excute");
        return $sqlUnit->process($callsp)->results;
    }catch(Exception $e){
        return $e;
    }
}; 