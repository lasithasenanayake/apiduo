<?php

class MsSqlConnectionPool {
    
    private $dbcon;

    public function getConnection(){
        if(!$this->dbcon){
            if(DYNAMIC_CONNECTION){
                $file=SQL_CONNECTION_PATH."/".ENTITY.".json";
                if(isset($_SESSION["ENTITY"])){
                    $file=SQL_CONNECTION_PATH."/".$_SESSION["ENTITY"].".json";
                }
                //echo $file;
                if(file_exists($file)){
                    $conObj =json_decode(file_get_contents($file));
                    $connectionInfo = array( "Database"=>$conObj->dbname, "UID"=>$conObj->username, "PWD"=>$conObj->password,"ConnectionPooling"=>0,"CharacterSet" => "UTF-8");
                    $this->dbcon= sqlsrv_connect( $conObj->servername, $connectionInfo);
                    if(!$this->dbcon){
                        throw new Exception(sqlsrv_errors()[0]["message"]);
                    }
                }else{
                    throw new Exception("Restricted access for this entity ". ENTITY. " Please contact system Admin");
                }
            }else{
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