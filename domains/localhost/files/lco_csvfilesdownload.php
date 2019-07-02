<?php
        /**
         * Developer :Lasitha Senanayake
         * Date : May 10 2019
         * Comments: Get Renew Calculation 
         * email :lasitha.senanayake@gmail.com
         * github : https://github.com/lasithasenanayake
         * company: Duo Software  
         */
return function ($context){
    $request = $context->getRequest();
    
    //runkit_constant_remove("ENTITY");
    //define ("ENTITY", $request->Params()->entity);
    $_SESSION["ENTITY"]=$request->Params()->entity;
    require_once(TENANT_RESOURCE_PATH."/bo_lib/master/lco_op.php");
    
    $sqlUnit = $context->resolve("mssql:query");
    $lco =new lco($sqlUnit);
    $l=$lco->get_lcobycode($request->Params()->lcoid);
    //return $l;
    $lcocode="nofile";
    $gulcoid="";
    if(!isset($l)){
        throw new Exception('LCO was not found');
        exit();
    }else{
        $lcocode= $l->LCOCode;
        $gulcoid= $l->GULCOID;
        
        //echo iconv('UTF-8//IGNORE','ASCII',  $gulcoid);
        //return $l;
    }
    
    //return $filepath;
    $filename=$gulcoid;
    switch($request->Params()->type){
        case "B2B_B2C_7day":
            $filename.="_7day.csv";
        break;
        case "B2B_B2C_all":
            $filename.=".csv";
        break;
        case "B2B_B2C_expired":
            $filename.="_exp.csv";
        break;

    }
    $filepath=MEDIA_FOLDER."/files/".$request->Params()->entity."/".$filename;
    try{
        if(file_exists($filepath)){
            ///$filepath=MEDIA_FOLDER."/files/".$request->Params()->entity."/".$request->Params()->lcoid."_exp.csv";
            header("Content-type: text/csv");
            header("Content-Disposition: attachment; filename=".$lcocode.".csv");
            header("Pragma: no-cache"); 
            header("Expires: 0");
            echo file_get_contents($filepath);
           
        }else{
            header("Content-type: text/csv");
            header("Content-Disposition: attachment; filename=".$lcocode.".csv");
            header("Pragma: no-cache");
            header("Expires: 0");
            //echo "Requested csv has been put to processing que it will be generated shortly.\n";
            require_once(TENANT_RESOURCE_PATH."/bo_lib/master/file_csv_q.php");
            $l=new LCO_Pending();
            echo $l->addLCO($request->Params()->lcoid);
            //echo "Entity -".ENTITY;
            exit();
        }
    }catch(Exception $e){
        throw new Exception("Internal Error Requested files not found.");
        //echo "Internal Error Requested files not found.";
    }

}; 