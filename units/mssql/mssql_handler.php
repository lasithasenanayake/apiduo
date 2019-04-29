<?php

require_once  dirname(__FILE__) .  "/mssql_connection_pool.php";

class MsSqlHandler extends AbstractUnit {
    
    private static $connPool;
    private $dbcon;
    
    private function init(){
        if(!$this->dbcon){
            if (!isset(MsSqlHandler::$connPool))
                MsSqlHandler::$connPool = new MsSqlConnectionPool();
                
            $this->dbcon = MsSqlHandler::$connPool->getConnection();
        }
    }
    public function process ($input,$operation=null){
        if (!isset(MsSqlHandler::$connPool))
            MsSqlHandler::$connPool = new MsSqlConnectionPool();

        $this->dbcon = MsSqlHandler::$connPool->getConnection();

        if(!isset($operation))
            $operation = $this->getUrnInput();
        
        switch ($operation){
            case "query":
                return $this->query($input);
            case "insert":
                return $this->insert($input->tablename,$input->data);
            case "update":
                return $this->update();
            case "delete":
                return $this->delete();
            case "excute":
                return $this->excute($input);
            case "bulksend":
                return $this->BulkSend($input);
        }

    }

    public function insert($tablename,$data){
        $this->init();
        if( $this->dbcon ){
            var_dump($data);
            foreach ($data as $key => $obj) {
                $sql=$this->GetInsertStatment($tablename,$obj);

                $stmt = sqlsrv_prepare($this->dbcon, $sql, array());
                //return $stmt;
                if( !$stmt ) {
                    throw new Exception(sqlsrv_errors()[0]["message"]);
                }
                $result =sqlsrv_execute($stmt);
                //var_dump($sql);
                if($result!==false ){
                    $obj->result=$result;

                    //return $result;
                }else{
                    //var_dump(sqlsrv_errors());
                    $obj->result=sqlsrv_errors()[0]["message"];
                    //throw new Exception(sqlsrv_errors()[0]["message"]);
                }
            }
            return $data;
            //var_dump($result);
        
        }else{
            throw new Exception("Connection Closed");
        }
    }

    public function update(){

    }

    public function delete(){

    }

    private function GetInsertStatment($table,$data){
        $first=true;
        $insertPart1="Insert Into ".$table." (";
        $Insertstr="";
        //foreach ($data as $key => $obj) {
            $values="Values(";
                foreach (get_object_vars($data) as $Objkey => $Objvalue) {
                    //if($first){
                        $insertPart1.=$Objkey.",";
                    //}
                    switch(gettype($Objvalue)){
                        case "string":
                            $values.="'".str_replace("'","''",$Objvalue)."',";
                        break;
                        case "integer":
                            $values.="".$Objvalue.",";
                        break;
                        case "double":
                            $values.="".$Objvalue.",";
                        break;
                        case "array":
                            //var_dump($Objvalue);
                            if(isset($Objvalue[0])){
                                $values.="'".date('m/d/y H:i:s',$Objvalue[0])."',";
                            }
                            //if(isset($Objvalue[0])){
                                //$values.="'".date('m/d/y H:i:s',$Objvalue[0])."',";
                            //}
                        break;
                        default:
                            $values.="'".$Objvalue."',";
                        break;
                    }
                }
                //if($first){
                    $insertPart1=rtrim($insertPart1,",").") ";
                    $first=false;
                    
                //}
                $values=rtrim($values,",").")";
                $Insertstr.=$insertPart1.$values;
                //echo $Insertstr;
        return $Insertstr;
    }
    public function BulkSend($input){
        if(count($input->Values)==0){
            return null;
        }
        //echo $this->GetBulKStatment($input);
        if( $this->dbcon ){
            $stmt = sqlsrv_prepare($this->dbcon,$this->GetBulKStatment($input), array());

            if( !$stmt ) {
                throw new Exception(sqlsrv_errors()[0]["message"]);
            }
            $result =sqlsrv_execute($stmt);
            
            //var_dump($result);
            if( $result){
                // Output params are now set,
                $input->results=$result;
                return $input; 
            }else{
                //var_dump(sqlsrv_errors());
                
                throw new Exception(sqlsrv_errors()[0]["message"]);
            }
        }
    }

    private function GetBulKStatment($input){
        $primary="";
        foreach ($input->PrimaryColumns as $key => $value) {
            $primary.=" s.".$value." = t.".$value." And";
        }
        $primary=rtrim($primary,"And");
        //echo $primary;
        $selection="(";
        $InsertValues="(";
        $updateValues="";
        foreach (get_object_vars($input->Values[0]) as $key => $value) {
            $selection.=$key.",";
            $InsertValues.="s.".$key.",";
            $updateValues.=$key."= "."s.".$key.",";
        }
        $selection=rtrim($selection,",").")";
        $InsertValues=rtrim($InsertValues,",").")";
        $updateValues=rtrim($updateValues,",");
        $values="";
        foreach ($input->Values as $key => $obj) {
            $values.="(";
            foreach (get_object_vars($obj) as $Objkey => $Objvalue) {
                switch(gettype($Objvalue)){
                    case "string":
                        $values.="'".$Objvalue."',";
                    break;
                    case "integer":
                        $values.="".$Objvalue.",";
                    break;
                    case "double":
                        $values.="".$Objvalue.",";
                    break;
                    case "array":
                        //var_dump($Objvalue);
                        if(isset($Objvalue[0])){
                            $values.="'".date('m/d/y H:i:s',$Objvalue[0])."',";
                        }
                        //if(isset($Objvalue[0])){
                            //$values.="'".date('m/d/y H:i:s',$Objvalue[0])."',";
                        //}
                    break;
                    default:
                        $values.="'".$Objvalue."',";
                    break;
                }
            }
            $values=rtrim($values,",")."),";
        }
        $values=rtrim($values,",");
        //echo $values."</br>";
        $update ="MERGE ".$input->Table." AS t
        USING (VALUES ".$values."
            ) AS s ".$selection."
                ON ".$primary."
        WHEN MATCHED THEN 
            UPDATE 
            SET    ".$updateValues."
        WHEN NOT MATCHED THEN 
            INSERT ".$selection."
            VALUES ".$InsertValues.";";
        return $update;
    }

    public function excute($input){
        //$sql = "EXEC stp_Create_Item @Item_ID = ?, @Item_Name = ?";
        if( $this->dbcon ){
            $stmt = sqlsrv_prepare($this->dbcon, $input->sql, $input->parameters);

            if( !$stmt ) {
                throw new Exception(sqlsrv_errors()[0]["message"]);
            }
            $result =sqlsrv_execute($stmt);
            
            //var_dump($result);
            if( $result){
                do
                {
                    $objectlist = array();
                    while($res = sqlsrv_fetch_object($stmt)){
                        // make sure all result sets are stepped through, since the output params may not be set until this happens
                            array_push($objectlist,$res);
                    }
                } while ( sqlsrv_next_result($stmt) ) ;
                
                // Output params are now set,
                $input->results=$objectlist;
                return $input; 
            }else{
                //var_dump(sqlsrv_errors());
                throw new Exception(sqlsrv_errors()[0]["message"]);
            }
        }else{
            throw new Exception("Connection Closed");

        }
    }

    private function GetLastResult($stmt)
    {
        $finalResult = false;
        do
        {
                // Use $stmt, e.g. store result in $finalResult to return as the final result 
                // useful functions: sqlsrv_num_fields, sqlsrv_has_rows, sqlsrv_rows_affected, sqlsrv_fetch_..., etc. 
        } while ( sqlsrv_next_result($stmt) ) ;
        return $finalResult; 
    }

    public function query($input){
        if( $this->dbcon ){
            $objectlist = array();
            //echo $input;
            //var_dump($input)
            
            $stmt = sqlsrv_query( $this->dbcon, $input);
            if( $stmt === false ) {
                throw new Exception(sqlsrv_errors()[0]["message"]);
                //die( print_r( sqlsrv_errors(), true));
            }
            while( $obj = sqlsrv_fetch_object( $stmt)) {
                //echo $obj->fName.", ".$obj->lName."<br />";
                array_push($objectlist,$obj);
            }
            //$this->close();
            return $objectlist;

        }else{
            //echo $input;
            throw new Exception(sqlsrv_errors()[0]["message"]);
        }
    }


    
}
