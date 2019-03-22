<?php

return function ($context){
    $request = $context->getRequest();
    //echo $request->Body();
    $body=json_decode($request->Body());
    try{
        require_once(TENANT_RESOURCE_PATH."/bo_lib/master/lco_op.php");
        //var_dump($body);
        $callsp =new stdClass();
        $callsp->name="SP_PrePaid_Renew_BasePack_NewTariff_VTest";
        $callsp->sql="EXEC SP_PrePaid_Renew_BasePack_NewTariff_V1 @AccountNo = ?, @PeriodInMonths = ?,
            @CreateUser= ?,@TransID= ?,@VCno= ?,
            @CheckAmount= ?,@QuickRenew= ?,@GUProPlanID= ?,@GUPackageID= ?,@CurrntExpiryDate= ?,@CasType= ?,
            @GUAccountID= ?,@GetDate=?,@GULCOID=?";
        //var_dump($body);
        $sqlUnit = $context->resolve("mssql:query");
        $lco =new lco($sqlUnit);
        $lcoobject=$lco->get_lcobyvcno($body->VCno);
        
        
        $callsp->parameters=array(array(&$body->AccountNo,SQLSRV_PARAM_IN),
            array(&$body->PeriodInMonths,SQLSRV_PARAM_IN),
            array(&$body->CreateUser,SQLSRV_PARAM_IN),
            array(&$body->TransID,SQLSRV_PARAM_IN),
            array(&$body->VCno,SQLSRV_PARAM_IN),
            array(&$body->CheckAmount,SQLSRV_PARAM_IN),
            array(&$body->QuickRenew,SQLSRV_PARAM_IN),
            array(&$body->GUProPlanID,SQLSRV_PARAM_IN),
            array(&$body->GUPackageID,SQLSRV_PARAM_IN),
            array(&$body->CurrntExpiryDate,SQLSRV_PARAM_IN),
            array(&$body->CasType,SQLSRV_PARAM_IN),
            array(&$body->GUAccountID,SQLSRV_PARAM_IN),
            array(date("m-d-Y H:i:s"),SQLSRV_PARAM_IN),
            array(&$lcoobject->GULCOID,SQLSRV_PARAM_IN));
        $sqlUnit = $context->resolve("mssql:excute");
        return $sqlUnit->process($callsp)->results;
    }catch(Exception $e){
        return $e;
    }
}; 