<?php
    require_once (dirname(__FILE__) . "/configloader.php");
    require_once (dirname(__FILE__) . "/init.php");
    SOSSPlatform::intialize();

    require_once (PLUGIN_PATH . "/SQLDB/connection.php");
    $sql = new SQLDataConnector();
    $sql->Open("dendb");
    
    $dbobj= $sql->getQuery("select * from tablex");
    echo json_encode($dbobj);
    $sql->Close();
?>