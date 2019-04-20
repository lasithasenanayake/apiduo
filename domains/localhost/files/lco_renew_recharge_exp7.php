<?php
return function ($context){
    $request = $context->getRequest();
    
    
    $filepath=MEDIA_FOLDER."/files/".ENTITY."/".$request->Params()->lcoid."_7day.csv";
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
            $timedif = (time() - filemtime($filepath));
            if ($timedif > 3600*96) {
                require_once(TENANT_RESOURCE_PATH."/bo_lib/master/file_csv_q.php");
                $l=new LCO_Pending();
                echo $l->addLCO($request->Params()->lcoid);
                exit();
            }else{
                echo file_get_contents($filepath);
                exit();
            }
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