<?php
    //echo microtime();
    //$t = microtime(true);
    //$micro = sprintf("%06d",($t - floor($t)) * 1000000);
    //$d = new DateTime( date('Y-m-d H:i:s.'.$micro, $t) );
    
    //print $d->format("Y-m-d").'T'.$d->format("H:i:s.u");

    define ("BASE_PATH", dirname(__FILE__));
    define ("CORE_PATH", BASE_PATH . "/core");
    define ("TENANT_PATH", BASE_PATH. "/domains");
    define ("UNIT_PATH", BASE_PATH. "/units");
    define ("MEDIA_FOLDER", "D:\\media");
    define ("DYNAMIC_CONNECTION", true);
    define ("SQL_CONNECTION_PATH",BASE_PATH . "/connections");
    define ("TENANT_RESOURCE_PATH", TENANT_PATH . "/localhost");
    define ("REQUEST_PATH", $_SERVER['REQUEST_URI']);
    define ("LOGGER", true);
    date_default_timezone_set('UTC');

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

    if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
    {
      define ("IP", $_SERVER['HTTP_CLIENT_IP']);
      //$ip=;
    }
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
    {
      define ("IP", $_SERVER['HTTP_X_FORWARDED_FOR']);
      
    }
    else
    {
      //$ip=$_SERVER['REMOTE_ADDR'];
      define ("IP", $_SERVER['REMOTE_ADDR']);
    }

    
    require_once (BASE_PATH . "/lib/phpcache/cache.php");
    // end duo configurations
    require_once (BASE_PATH . "/davvag_api_manager.php");
    DavvagApiManager::start();
?>