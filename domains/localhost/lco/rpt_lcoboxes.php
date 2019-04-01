<?php
return function ($context){
    $request = $context->getRequest();
    //echo $request->Body();
    $body=json_decode($request->Body());
    //try{
        $cacheObj=CacheData::getObjects($request->Params()->lastid."-".$request->Params()->lcocode,"box_calculation");
            if($cacheObj){
                  //return $cacheObj;
            }
        require_once(TENANT_RESOURCE_PATH."/bo_lib/master/account.php");
        require_once(TENANT_RESOURCE_PATH."/bo_lib/master/accountrenew.php");
        require_once(TENANT_RESOURCE_PATH."/bo_lib/master/lco_op.php");
        $sqlUnit = $context->resolve("mssql:query");
        $mainDoc =new stdClass();
        $report=array();
       
        $Account =new Account($sqlUnit);
        $lco =new lco($sqlUnit);
        if($request->Params()->lastid==0){
            //$sql=""
            $mainDoc->record_count=$lco->get_lcos_accounts_count($request->Params()->lcocode);
            $rcount=$mainDoc->record_count;
            $mainDoc->pages=array();
            $pagenumber=0;
            $lastidcount=0;
            $pagesize=20;
            while($rcount>=0){
                $s=new stdClass();
                $s->page=$pagenumber;
                $s->lastid=$lastidcount;
                $lastidcount+=$pagesize;
                $rcount-=$pagesize;
                array_push($mainDoc->pages,$s);
                $pagenumber++;
                //var_dump($s);
            }
            
        }
        $results=$lco->get_lcos_accounts($request->Params()->lastid,$request->Params()->lcocode);
        //var_dump($results);
        foreach ($results as $key => $value) {
            # code...
            $sqlUnit = $context->resolve("mssql:query");
            $Accountobject=$Account->get_accountbyvc($value->VCNo);
            $Accountobject->entitlements=$Account->get_entitlements();
            $sqlUnit = $context->resolve("mssql:excute");
            $renew =new AccountRenewOp($sqlUnit);
           
            $item= $renew->CalacuteRenewPrices($Accountobject,"quick");
            $item->id=$value->rnbr;
            //var_dump($value);
            $mainDoc->lastid=$value->rnbr;
            array_push($report,$item);
        }
        
        $mainDoc->results=$report;
        if(count($report)>0){
            CacheData::setObjects($request->Params()->lastid."-".$request->Params()->lcocode,"box_calculation",$mainDoc);
        }
        return  $mainDoc;
       
}; 
