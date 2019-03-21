<?php

require_once  dirname(__FILE__) .  "/mssql_connection_pool.php";

class MsSqlHandler extends AbstractUnit {
    
    private static $connPool;
    private $dbcon;
    
    public function process ($input){
        if (!isset(MsSqlHandler::$connPool))
            MsSqlHandler::$connPool = new MsSqlConnectionPool();

        $this->dbcon = MsSqlHandler::$connPool->getConnection();

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
        if( $this->dbcon ){
            $stmt = sqlsrv_prepare($this->dbcon, $input->sql, $input->parameters);

            if( !$stmt ) {
                throw new Exception(sqlsrv_errors()[0]["message"]);
            }
            $objectlist = array();
            if(sqlsrv_execute($stmt)){
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
            // /throw new Exception(sqlsrv_errors()[0]["message"]);
        }
    }


    
}
