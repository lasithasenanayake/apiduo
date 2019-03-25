<?php

class MsSqlConnectionPool {
    
    private $dbcon;

    public function getConnection(){
        if(!$this->dbcon){
            $connectionInfo = DavvagApiManager::$tenantConfiguration["configuration"]["mssql"];
            //echo "open";
            $this->dbcon= sqlsrv_connect( $connectionInfo["servername"], $connectionInfo["parameters"]);
            if( $this->dbcon ) {
                //echo "Connection established.<br />";
            }else{
                //var_dump(sqlsrv_errors()[0]["message"]);
                throw new Exception(sqlsrv_errors()[0]["message"]);
                //echo "Connection could not be established.<br />";
                //die( print_r( sqlsrv_errors(), true));
            }

            DavvagApiManager::addAction("end", array($this,"closeConnection"));
        }

        return $this->dbcon;
    }

    public function closeConnection(){
        if($this->dbcon){
            sqlsrv_close( $this->dbcon );
        }else{
            throw new Exception("No Connection to Close.");
        }
    }
}