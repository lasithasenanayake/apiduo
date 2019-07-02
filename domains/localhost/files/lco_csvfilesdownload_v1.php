<?php
        /**
         * Developer :Lasitha Senanayake
         * Date : May 28 2019
         * Comments: CSV Caculation Download v1
         * email :lasitha.senanayake@gmail.com
         * github : https://github.com/lasithasenanayake
         * company: Duo Software  
         */
function GetFromQuery($sqlstr){
    require_once(TENANT_RESOURCE_PATH."/sqlsvr/sqlsvr.php");
    $sql =new SqlConnector("report");
    $out=$sql->GetSQLtoCSV($sqlstr);
    return $out;
}
return function ($context){
    $request = $context->getRequest();
    $_SESSION["ENTITY"]=$request->Params()->entity;
    $entity=$request->Params()->entity;
    $lcocode=$entity."-".$request->Params()->lcoid;
    $gulcoid="";
    
    if($request->Params()->lcoid!="all"){
        require_once(TENANT_RESOURCE_PATH."/bo_lib/master/lco_op.php");
        
        $sqlUnit = $context->resolve("mssql:query");
        $lco =new lco($sqlUnit);
        $l=$lco->get_lcobycode($request->Params()->lcoid);
        //return $l;
        $lcocode="nofile";
        $gulcoid="";
        if(!isset($l)){
            $lcocode=$request->Params()->lcoid;
            $gulcoid="";
        }else{
            $lcocode= $l->LCOCode;
            $gulcoid= $l->GULCOID;
            
            //echo iconv('UTF-8//IGNORE','ASCII',  $gulcoid);
            //return $l;
        }
    }
    
    //return $filepath;
    $filename=$gulcoid;
    switch($request->Params()->type){
        case "B2B_B2C_7day":
            $out=GetFromQuery("Select * From ".$entity."_csv_LCOAccounts Where gulcoid='".$gulcoid."' and (DATEDIFF(Day, GETDATE(), ExpiryDate) BETWEEN 1 AND 7) and (Status = 'active')");
        break;
        case "B2B_B2C_all":
            $out=GetFromQuery("Select * From ".$entity."_csv_LCOAccounts  Where gulcoid='".$gulcoid."'");
        $filename.=".csv";
        break;
        case "B2B_B2C_expired":
            $out=GetFromQuery("Select * From ".$entity."_csv_LCOAccounts  Where gulcoid='".$gulcoid."' and (Status = 'inactive')");
        break;
        case "B2B_B2C_Summery":
            $out=GetFromQuery("Select * From ".$entity."_Summery_exp7days");
            break;

    }
    //$filepath=MEDIA_FOLDER."/files/".$request->Params()->entity."/".$filename;
    try{
        if(isset($out)){
            ///$filepath=MEDIA_FOLDER."/files/".$request->Params()->entity."/".$request->Params()->lcoid."_exp.csv";
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Content-Length: " . strlen($out));
            header("Content-type: text/x-csv");
            header("Content-Disposition: attachment; filename=".$lcocode."-".$request->Params()->type."-".date("mdY").".csv");
            header("Pragma: no-cache"); 
            header("Expires: 0");
            echo trim($out);
            exit();
        }else{
            header("Content-type: text/csv");
            header("Content-Disposition: attachment; filename=".$lcocode.".csv");
            header("Pragma: no-cache");
            header("Expires: 0");
            //echo "Requested csv has been put to processing que it will be generated shortly.\n";
            if($gulcoid!=""){
                require_once(TENANT_RESOURCE_PATH."/bo_lib/master/file_csv_q.php");
                $l=new LCO_Pending();
                echo $l->addLCO($request->Params()->lcoid);
            }else{
                echo "requested not found.";
            }
            //echo "Entity -".ENTITY;
            exit();
        }
    }catch(Exception $e){
        throw new Exception("Internal Error Requested files not found.");
        //echo "Internal Error Requested files not found.";
    }

}; 