<?php
phpinfo();
/*
$all=array();
$test=new stdClass();
$test->Key1='datakeyA1';
$test->Key2=14.50;
$test->Key3=getdate();
$test->Val='somevaluetoinsertorupdate';
array_push($all,$test);
$test=new stdClass();
$test->Key1='datakeyA2';
$test->Key2=floatval("0.50");
$test->Key3=getdate();
$test->Val='somevaluetoinsertorupdate';
array_push($all,$test);
//array_push($all,$test);
$test=new stdClass();
$test->Key1='datakeyA3';
$test->Key2=0;
$test->Key3=getdate();
$test->Val='somevaluetoinsertorupdate';
array_push($all,$test);
$input=new stdClass();
$input->Table="tmp_tableA";
//$columns=array("Key1","Key2");

$input->PrimaryColumns=array("Key1","Key2");
$input->Values=$all;

$primary="";
foreach ($input->PrimaryColumns as $key => $value) {
    $primary.=" s.".$value." = t.".$value." And";
    //"s.Key1 = t.Key1"
}
$primary=rtrim($primary,"And");
//echo $primary;
$selection="(";
$InsertValues="(";
$updateValues="";
foreach (get_object_vars($input->Values[0]) as $key => $value) {
    $selection.=$key.",";
    $InsertValues.=$key." = "."s.";
    $updateValues.=$key."= "."s.".$key.",";

}

//echo $selection;
$selection=rtrim($selection,",").")";
$InsertValues=rtrim($InsertValues,",").")";
echo rtrim($updateValues,",");
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
echo $values."</br>";
//$values = 
$update ="MERGE ".$input->Table." AS t
USING (VALUES ".$values."
    ) AS s ".$selection."
        ON ".$primary."
WHEN MATCHED THEN 
    UPDATE 
    SET    Val = s.Val
WHEN NOT MATCHED THEN 
    INSERT ".$selection."
    VALUES ".$InsertValues.";";

echo $update;*/
 
?>