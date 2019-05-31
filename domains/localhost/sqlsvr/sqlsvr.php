<?php
class SqlConnector{
    private $dbcon;

    function __construct($entity){
        if(file_exists(SQL_CONNECTION_PATH."/".$entity.".json")){
            $conObj =json_decode(file_get_contents(SQL_CONNECTION_PATH."/".$entity.".json"));
            
            $connectionInfo = array( "Database"=>$conObj->dbname, "UID"=>$conObj->username, "PWD"=>$conObj->password,"ConnectionPooling"=>0,"CharacterSet" => "UTF-8");
            $this->dbcon= sqlsrv_connect( $conObj->servername, $connectionInfo);
            if(!$this->dbcon){
                throw new Exception(sqlsrv_errors()[0]["message"]);
            }
        }else{
            throw new Exception("Restricted access for this entity ". entity. " Please contact system Admin");
        }
    }

    public function Close(){
        if($this->dbcon){
            sqlsrv_close( $this->dbcon );
        }else{
            throw new Exception("No Connection to Close.");
        }
    }

    public function BulkSend($input){
        //var_dump($input->Values);
        if(count($input->Values)==0){
            return null;
        }
        //echo $this->GetBulKStatment($input);
        if( $this->dbcon ){
            DavvagApiManager::log("mysql-bulksend","info","Start Bulk send operations");
            $stmt = sqlsrv_prepare($this->dbcon,$this->GetBulKStatment($input), array());

            if( !$stmt ) {
                throw new Exception(sqlsrv_errors()[0]["message"]);
            }
            $result =sqlsrv_execute($stmt);
            
            //var_dump($result);
            if( $result){
                // Output params are now set,
                DavvagApiManager::log("mysql-bulksend","info","End Bulk send operations");

                $input->results=$result;
                return $input; 
            }else{
                //echo $this->GetBulKStatment($input);
                DavvagApiManager::log("mysql-bulksend","error",sqlsrv_errors()[0]["message"]);
                //DavvagApiManager::log("mysql-bulksend","error",sqlsrv_errors()[0]["message"]);
                throw new Exception(sqlsrv_errors()[0]["message"]);
            }
        }
    }

    public function GetSQLtoCSV($sql){
        $csv_terminated = "\n";
        $csv_separator = ",";
        $csv_enclosed = '"';
        $csv_escaped = "\\";
        $result = sqlsrv_query( $this->dbcon, $sql);
        if( $result === false) {
            die( print_r( sqlsrv_errors(), true));
         }
        $fields_cnt = sqlsrv_num_fields($result);
        $schema_insert = '';
        $out='';
        foreach( sqlsrv_field_metadata( $result ) as $fieldMetadata ) {
            $schema_insert .= $fieldMetadata["Name"].$csv_separator;
        }
        $out = trim(substr($schema_insert, 0, -1));
        $out.= $csv_terminated;
        //var_dump($out);
        //exit();
        while ($row = sqlsrv_fetch_array($result))
        {
            $schema_insert='';
            for ($i = 0; $i < $fields_cnt; $i++)
            {
                $Objvalue=$row[$i];
                $values="";
                switch(gettype($row[$i])){
                    case "string":
                        $values=$csv_enclosed .$Objvalue. $csv_enclosed;
                    break;
                    case "integer":
                        $values=strval($Objvalue);
                    break;
                    case "double":
                        $values=strval($Objvalue);
                    break;
                    case "array":
                        if(isset($Objvalue[0]) && isset($Objvalue->month)){
                            $values=$csv_enclosed .date('m/d/y',$Objvalue[0]).$csv_enclosed ;
                        }
                    case "object":
                        $values= $csv_enclosed .$Objvalue->format('m-d-Y').$csv_enclosed;
                        
                    break;
                    default:
                        $values=$csv_enclosed .$Objvalue.$csv_enclosed;
                    break;
                }
                $schema_insert.= $values.$csv_separator;
            } // end for
            $out.= trim(substr($schema_insert, 0, -1));
            $out.= $csv_terminated;
        }
       
        return $out;
       
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
                        if(isset($Objvalue[0]) && isset($Objvalue->month)){
                            $values.="'".date('m/d/y H:i:s',$Objvalue[0])."',";
                        }
                    case "object":
                        //var_dump($Objvalue);
                        if(isset($Objvalue->date)){
                            //var_dump(date_create($Objvalue->date));
                            //var_dump(date('m/d/y H:i:s', strtotime($Objvalue->date)));
                            $values.="'".date('m/d/y H:i:s', strtotime($Objvalue->date))."',";
                        }else{
                            $values.="null,";
                        }
                    break;
                    default:
                        //echo gettype($Objvalue);
                        //var_dump($Objvalue);
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
}
?>