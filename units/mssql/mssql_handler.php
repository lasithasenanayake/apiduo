<?php
class MsSqlHandler extends AbstractUnit {
    
    private $dbcon;
    
    public function Open(){
        if(!$this->dbcon){
            echo "open";
            $connectionInfo = DavvagApiManager::$tenantConfiguration["configuration"]["mssql"];

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
    }

    public function Close(){
        if( $this->dbcon ){
            sqlsrv_close( $this->dbcon );
        }else{
            throw new Exception("No Connection to Close.");
        }
    }

    public function process ($input){
        $operation = $this->getUrnInput();
        switch ($operation){
            case "query":
                return $this->query($input);
            case "insert":
                return $this->insert();
            case "update":
                return $this->update();
            case "delete":
                return $this->delete();
            case "excute":
                return $this->excute();
        }

    }

    public function insert(){

    }

    public function update(){

    }

    public function delete(){

    }

    public function excute(){

    }

    public function query($input){
        $this->Open();
        if( $this->dbcon ){
            $objectlist = array();
            //echo $input;
            $stmt = sqlsrv_query( $this->dbcon, $input);
            if( $stmt === false ) {
                die( print_r( sqlsrv_errors(), true));
            }
            while( $obj = sqlsrv_fetch_object( $stmt)) {
                //echo $obj->fName.", ".$obj->lName."<br />";
                array_push($objectlist,$obj);
            }
            //$this->close();
            return $objectlist;

        }else{
            throw new Exception(sqlsrv_errors()[0]["message"]);
        }
    }


    
}
