<?php

class SearchServices {
    public function getaccount($req){
        require_once (PLUGIN_PATH . "/SQLDB/connection.php");
        $sql = new SQLDataConnector();
        $sql->Open("dentest");
        $dbobj= $sql->getQuery("select top 100 * from a_CustAccountInformation where Accountno=".$_GET["accno"]);
        $sql->Close();
        return $dbobj;
    }

    public function gettest($req){
        
        $sall=$req->Body(true);
        return $sall;
    }

    public function getattribute($req){
        
    }
}

?>