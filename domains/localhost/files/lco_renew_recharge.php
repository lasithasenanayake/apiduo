<?php
return function ($context){
    $request = $context->getRequest();
    
    
    $filepath=MEDIA_FOLDER."/files/".ENTITY."/".$request->Params()->lcoid.".csv";
    require_once(TENANT_RESOURCE_PATH."/bo_lib/master/lco_op.php");
    $sqlUnit = $context->resolve("mssql:query");
    $lco =new lco($sqlUnit);
    $l=$lco->get_lco("gulcoid",$request->Params()->lcoid);
    $lcocode="null";
    if(!isset($l) || count($l)==0){
        throw new Exception('LCO was not found');
        exit();
    }else{
        $lcocode= $l[0]->LCOCode;
    }
    //return $filepath;
    try{
        if(file_exists($filepath)){
            header("Content-type: text/csv");
            header("Content-Disposition: attachment; filename=".$lcocode.".csv");
            header("Pragma: no-cache"); 
            header("Expires: 0");
            echo file_get_contents($filepath);
            exit();
        }else{
            header("Content-type: text/csv");
            header("Content-Disposition: attachment; filename=".$lcocode.".csv");
            header("Pragma: no-cache");
            header("Expires: 0");
            echo "Requested csv has been not generated please contact system Admin.\n";
            echo "Entity -".ENTITY;
            
            exit();
        }
    }catch(Exception $e){
        throw new Exception("Internal Error Requested files not found.");
        //echo "Internal Error Requested files not found.";
    }

}; 