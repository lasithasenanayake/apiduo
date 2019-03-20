<?php
    require_once (dirname(__FILE__) . "/configloader.php");
    //require_once (dirname(__FILE__) . "/init.php");
    //SOSSPlatform::intialize();
    //header('Content-Type: application/json');
    //for testing sql connector
    require_once (PLUGIN_PATH . "/SQLDB/connection.php");
    $sql = new SQLDataConnector();
    $sql->Open("dentest");
    
    $dbobj= $sql->getQuery("select top 100 * from a_CustAccountInformation where Accountno=".$_GET["accno"]);
    /*
    foreach ($dbobj as $key => $value) {
        echo "table sample test start </br>";
        echo $value->tempid."</br>";
        echo $value->mycol1."</br>";
        echo $value->mycol2."</br>";
    }*/
    header('Content-Type: application/json');
    print_r(json_encode($dbobj));
    $sql->Close();
?>