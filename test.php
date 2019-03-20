<?php
    require_once (dirname(__FILE__) . "/configloader.php");
    require_once (BO_PATH . "/lco/init.php");
    require_once (PLUGIN_PATH . "/SQLDB/connection.php");
    try{
        $sql = new SQLDataConnector();
        $sql->Open("dentest");
        $lco=new lcoLedger($sql);
        echo $lco->GetBalance($_GET["lcocode"]);
        $sql->Close();
    }catch(Exception $e){
        echo $e->getMessage();
    }
?>