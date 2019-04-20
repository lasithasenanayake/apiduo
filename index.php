<?php
    define ("BASE_PATH", dirname(__FILE__));
    define ("CORE_PATH", BASE_PATH . "/core");
    define ("TENANT_PATH", BASE_PATH. "/domains");
    define ("UNIT_PATH", BASE_PATH. "/units");
    define ("MEDIA_FOLDER", "D:\\media");
    define ("DYNAMIC_CONNECTION", true);
    define ("SQL_CONNECTION_PATH",BASE_PATH . "/connections");
    define ("TENANT_RESOURCE_PATH", TENANT_PATH . "/localhost");
    $headers=getallheaders();
    if(isset($headers["entity"])){
        define ("ENTITY", $headers["entity"]);
    }else{
        if(DYNAMIC_CONNECTION){
            define ("ENTITY", "82-crystalvision");
        }else{
            define ("ENTITY", $_SERVER["HTTP_HOST"]);
        }
    }
    if(isset($headers["duoapikey"])){
        define ("APIKEY", $headers["duoapikey"]);
    }else{
        define ("APIKEY", "anonymous");
    }
    
    
    require_once (BASE_PATH . "/lib/phpcache/cache.php");
    // end duo configurations
    require_once (BASE_PATH . "/davvag_api_manager.php");
    DavvagApiManager::start();
?>