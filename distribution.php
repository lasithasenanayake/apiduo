<?php

//Duo Configurations
define ("MEDIA_FOLDER", "D:\\media");
define ("DYNAMIC_CONNECTION", true);
define ("SQL_CONNECTION_PATH",dirname(__FILE__) . "/connections");
//define ("ENTITY", "dentest");
//require_once (CORE_PATH . "/autoloader.php");
//spl_autoload_register("Autoloader::load");
// Header request for Connection Information
$headers=getallheaders();
if(isset($headers["entity"])){
    define ("ENTITY", $headers["entity"]);
}else{
    if(DYNAMIC_CONNECTION){
        define ("ENTITY", "82-dendb");
    }else{
        define ("ENTITY", $_SERVER["HTTP_HOST"]);
    }
}

require_once (dirname(__FILE__) . "/lib/phpcache/cache.php");
// end duo configurations


