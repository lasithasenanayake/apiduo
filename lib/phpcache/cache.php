<?php
// a function to receive an write some data into a file
//require_once (dirname(__FILE__) . "/../../configloader.php");

class CacheData{
    public static function getObjects($lastVersionId,$className,$hours=1){
        try{
        $tenantId = ENTITY;
        $path = MEDIA_FOLDER . "/cache/".  ENTITY . "/".$className;
        $lastVersionId= md5($lastVersionId);
        if (!file_exists($path))
              mkdir($path, 0777, true);

        //$path=$tenantId
        $path=$path."/".$lastVersionId.".chr";
        if (file_exists($path)){
            $timedif = (time() - filemtime($path));
            if ($timedif < 3600*$hours) {
                $f = fopen($path, 'r');
                $buffer = '';
                while(!feof($f)) {
                    $buffer .= fread($f, 2048);
                }
                fclose($f);
                return json_decode($buffer);
            }else{
                unlink($path);
                return null;
            }
        }else{
            return null;
        }
        }catch(Exception $e){
            return null;
        }
    }

    public static function getObjects_fullcache($lastVersionId,$className){
        try{
        $tenantId = ENTITY;
        $path = MEDIA_FOLDER . "/cache/".  ENTITY . "/".$className;
        $lastVersionId= md5($lastVersionId);
        if (!file_exists($path))
              mkdir($path, 0777, true);

        //$path=$tenantId
        $path=$path."/".$lastVersionId.".chr";
        if (file_exists($path)){
           
                $f = fopen($path, 'r');
                $buffer = '';
                while(!feof($f)) {
                    $buffer .= fread($f, 2048);
                }
                fclose($f);
                return json_decode($buffer);
            
            }else{
            return null;
        }    
        }catch(Exception $e){
            return null;
        }
    }

    public static function clearObjects($className){
        try{
            if($className==""){
                return;
            }
            $path = MEDIA_FOLDER . "/cache/".  ENTITY . "/$className";
            if (file_exists($path)){
                array_map('unlink', glob("$path/*.*"));
                rmdir($path);
            }
        }catch(Exception $e){
            return null;
        }
    }
    public static function setObjects($lastVersionId,$className, $saveObj){
        try{
            $lastVersionId= md5($lastVersionId);
            $tenantId = ENTITY;
            $path = MEDIA_FOLDER . "/cache/".  ENTITY . "/$className";
                
            if (!file_exists($path))
                mkdir($path, 0777, true);

            //$path=$tenantId
            $string=json_encode($saveObj);
            $path=$path."/".$lastVersionId.".chr";
            $f = fopen($path, 'w');
            fwrite ($f, $string, strlen($string));
            fclose($f);
        }catch(Exception $e){
            
        }
        
    }
}


?>