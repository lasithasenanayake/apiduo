<?php
    define ("BASE_PATH", dirname(__FILE__));
    define ("CORE_PATH", BASE_PATH . "/core");
    define ("TENANT_PATH", BASE_PATH. "/domains");
    //define ("TENANT_RESOURCE_PATH", TENANT_PATH . "/$_SERVER[HTTP_HOST]"); // removed to hardcode one tenant access only.
    define ("UNIT_PATH", BASE_PATH. "/units");
    //Duo Configurations
    define ("MEDIA_FOLDER", "D:\\media");
    define ("DYNAMIC_CONNECTION", true);
    define ("SQL_CONNECTION_PATH",BASE_PATH . "/connections");
    define ("TENANT_RESOURCE_PATH", TENANT_PATH . "/localhost");
    //define ("ENTITY", "dentest");
    //require_once (CORE_PATH . "/autoloader.php");
    //spl_autoload_register("Autoloader::load");
    // Header request for Connection Information
    $headers=getallheaders();
    if(isset($headers["entity"])){
        define ("ENTITY", $headers["entity"]);
    }else{
        if(DYNAMIC_CONNECTION){
            define ("ENTITY", "dendb");
        }else{
            define ("ENTITY", $_SERVER["HTTP_HOST"]);
        }
    }

    require_once (BASE_PATH . "/lib/phpcache/cache.php");
    // end duo configurations
    require_once (BASE_PATH . "/davvag_api_manager.php");
DavvagApiManager::start();