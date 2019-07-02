<?php

return function($context){
    $request = $context->getRequest();
    $tranactions= json_decode($request->Body());
    $SecurityVault=DavvagApiManager::$SercurityVault;
    $BlokTicket=$tranactions->blockticket;
    $Amount=$tranactions->amount;
    $ledgerTrans=$tranactions->transactions;
    $allowminus=0;
    DavvagApiManager::log("api","info","Start add ledger Tranaction for VID:".$BlokTicket->GUVaultID);
    if(isset($tranactions->allowminus)){
        //if($tranactions->allowminus)
        DavvagApiManager::log("api","info","Allow Minus:".$tranactions->allowminus);

        $allowminus=$tranactions->allowminus;
    }
    require_once(TENANT_RESOURCE_PATH."/bo_lib/vault/ledger.php");
        $vaultObj = new Ledger($context);
        $balance = $vaultObj->getBalance("lco",$BlokTicket->GUVaultID);
        //$balance+=$BlokTicket->Amount;
        DavvagApiManager::log("api","info","Amount :".$balance);
        foreach ($ledgerTrans as $key => $value) {
            # code...
            $balance-= $ledgerTrans[$key]->debit-$ledgerTrans[$key]->credit;
            $ledgerTrans[$key]->OpeningBalance=$balance;
        }
        $callsp =new stdClass();
        $callsp->name="A_LCOVaultBlock";
        $callsp->sql="EXEC A_LCOVaultUpdateBalance 
        @BlockID = ?, 
        @UtilizedAmout = ?,@GULCOID=?,@AllowMinus=?";
        $sqlUnit = $context->resolve("mssql:excute");
        $callsp->parameters=array(
        array($BlokTicket->BlockID,SQLSRV_PARAM_IN),//AccountNo
        array($Amount,SQLSRV_PARAM_IN),//GetDate
        array($BlokTicket->GUVaultID,SQLSRV_PARAM_IN),//GetDate
        array($allowminus,SQLSRV_PARAM_IN));//GetDate
        $results=$sqlUnit->process($callsp)->results;
        //var_dump($results);
        //return $results;
        foreach ($ledgerTrans as $key => $value) {
            # code...
            
            $ledgerTrans[$key]->ledgerid=$results[0]->ID;
            //str
        }
        DavvagApiManager::log("api","info","Ledger Tran ID  :".$results[0]->ID);
        //$results->ID
        $out =new stdClass();
        $out->TranID=$results[0]->ID;
        $sqlUnit = $context->resolve("mssql:insert");
        $out->items= $sqlUnit->insert("a_LCOLedger",$ledgerTrans);
        DavvagApiManager::log("api","info","Completed Ledger  :".$results[0]->ID);
        
        $redis = $context->resolve("redis:get");
        $redis->delete("vault-".ENTITY.$BlokTicket->GUVaultID."-lco");
        DavvagApiManager::log("api","info","Finish Ledger  :".$results[0]->ID);
        return $out;
    
};
?>