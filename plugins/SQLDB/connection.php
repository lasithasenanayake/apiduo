<?php
class SQLDataConnector {
    private $dbcon;
    
    public function Open($connection){
        $conObj =json_decode(file_get_contents(SQL_CONNECTION_PATH.$connection.".json"));
        $connectionInfo = array( "Database"=>$conObj->dbname, "UID"=>$conObj->username, "PWD"=>$conObj->password);
        $this->dbcon= sqlsrv_connect( $conObj->servername, $connectionInfo);
        if( $this->dbcon ) {
            //echo "Connection established.<br />";
        }else{
            //var_dump(sqlsrv_errors()[0]["message"]);
            throw new Exception(sqlsrv_errors()[0]["message"]);
            //echo "Connection could not be established.<br />";
            //die( print_r( sqlsrv_errors(), true));
        }
    }

    public function getQuery($query){
        if( $this->dbcon ){
            $objectlist = array();
            $stmt = sqlsrv_query( $this->dbcon, $query);
            if( $stmt === false ) {
                die( print_r( sqlsrv_errors(), true));
            }
            while( $obj = sqlsrv_fetch_object( $stmt)) {
                //echo $obj->fName.", ".$obj->lName."<br />";
                array_push($objectlist,$obj);
            }
            return $objectlist;

        }else{
            throw new Exception(sqlsrv_errors()[0]["message"]);
        }
    }

    public function Close(){
        if( $this->dbcon ){
            sqlsrv_close( $this->dbcon );
        }else{
            throw new Exception("No Connection to Close.");
        }
    }

}
?>