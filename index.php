<?php
    require_once (dirname(__FILE__) . "/configloader.php");
    require_once (dirname(__FILE__) . "/init.php");
    SOSSPlatform::intialize();

    //for testing sql connector
    require_once (PLUGIN_PATH . "/SQLDB/connection.php");
    $sql = new SQLDataConnector();
    $sql->Open("dendb");
    
    $dbobj= $sql->getQuery("select * from tablex");
    /*
    foreach ($dbobj as $key => $value) {
        echo "table sample test start </br>";
        echo $value->tempid."</br>";
        echo $value->mycol1."</br>";
        echo $value->mycol2."</br>";
    }*/
    echo json_encode($dbobj);
    $sql->Close();
?>