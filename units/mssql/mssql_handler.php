<?php
class MsSqlHandler extends AbstractUnit {
    
    private $dbcon;
    
    public function Open(){
        if(!$this->dbcon){
            //echo "open";
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

    public function process ($input,$operation=null){
        if(!isset($operation))
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
                return $this->excute($input);
        }

    }

    public function insert(){

    }

    public function update(){

    }

    public function delete(){

    }

    public function excute($input){
        //$sql = "EXEC stp_Create_Item @Item_ID = ?, @Item_Name = ?";
        $this->Open();
        if( $this->dbcon ){
            $stmt = sqlsrv_prepare($this->dbcon, $input->sql, $input->parameters);

            if( !$stmt ) {
                throw new Exception(sqlsrv_errors()[0]["message"]);
            }
            $objectlist = array();
            $result =sqlsrv_execute($stmt);
            if( $result){
                //var_dump($stmt);
                while($res = sqlsrv_fetch_object($stmt)){
                // make sure all result sets are stepped through, since the output params may not be set until this happens
                    array_push($objectlist,$res);
                }
                // Output params are now set,
                $input->results=$objectlist;
                return $input; 
            }else{
                throw new Exception(sqlsrv_errors()[0]["message"]);
            }
        }
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
