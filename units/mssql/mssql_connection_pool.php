<?php

class MsSqlConnectionPool {
   /**
         * Developer :Lasitha Senanayake
         * Date : May 1 2019
         * Comments: MS SQL Connection Pool
         * email :lasitha.senanayake@gmail.com
         * github : https://github.com/lasithasenanayake
         * company: Duo Software  
         */
    private $dbcon;

    public function getConnection(){
        
        if(!$this->dbcon){
            DavvagApiManager::log("mysql","info","Connection open Start.");
            if(DYNAMIC_CONNECTION){
                DavvagApiManager::log("mysql","info","Dynamic Connection.");
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
                        DavvagApiManager::log("mysql","error",sqlsrv_errors()[0]["message"]);

                        throw new Exception(sqlsrv_errors()[0]["message"]);
                    }
                }else{
                    DavvagApiManager::log("mysql","error","Restricted access for this entity ". ENTITY. " Please contact system Admin");
                    throw new Exception("Restricted access for this entity ". ENTITY. " Please contact system Admin");
                }
            }else{
                DavvagApiManager::log("mysql","info","Local yml Connection.");
                $connectionInfo = DavvagApiManager::$tenantConfiguration["configuration"]["mssql"];
                //echo "open";
                $this->dbcon= sqlsrv_connect( $connectionInfo["servername"], $connectionInfo["parameters"]);
                if( $this->dbcon ) {
                    //echo "Connection established.<br />";
                }else{
                    //var_dump(sqlsrv_errors()[0]["message"]);
                    DavvagApiManager::log("mysql","error",sqlsrv_errors()[0]["message"]);
                    throw new Exception(sqlsrv_errors()[0]["message"]);
                    //echo "Connection could not be established.<br />";
                    //die( print_r( sqlsrv_errors(), true));
                }
            }
            DavvagApiManager::addAction("end", array($this,"closeConnection"));
            DavvagApiManager::log("mysql","info","Connection open end.");
        }
        
        return $this->dbcon;
    }

    public function closeConnection(){
        if($this->dbcon){
            DavvagApiManager::log("mysql","info","Connection close.");
            sqlsrv_close( $this->dbcon );
        }else{
            DavvagApiManager::log("mysql","error","No Connection to Close.");
            throw new Exception("No Connection to Close.");
        }
    }

    
}