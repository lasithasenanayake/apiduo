<?php

require_once (CORE_PATH. "/configuration_manager.php");
require_once (CORE_PATH. "/route_manager.php");
require_once (CORE_PATH. "/dispatcher.php");
require_once (CORE_PATH. "/urnresolver.php");
require_once (CORE_PATH. "/abstract_unit.php");
require_once (CORE_PATH. "/invoke_source.php");
require_once (CORE_PATH. "/context.php");
require_once (CORE_PATH. "/event_manager.php");

class DavvagApiManager {
    
    public static $routeManager;
    public static $configurationManager;
    public static $mainConfig;
    public static $dispatcher;
    public static $resolver;
    public static $tenantConfiguration;
    public static $eventManager;
    public static $SercurityVault;
    public static $logObject;

    public static function start(){
        DavvagApiManager::$configurationManager = new ConfigurationManager();
        DavvagApiManager::$routeManager = new RouteManager();
        DavvagApiManager::$dispatcher = new Dispatcher();
        DavvagApiManager::$resolver = new UrnResolver();
        DavvagApiManager::$mainConfig = DavvagApiManager::$configurationManager->getMainConfiguration();
        DavvagApiManager::$tenantConfiguration = DavvagApiManager::$configurationManager->getTenantConfiguration();
        DavvagApiManager::$eventManager = new EventManager();
        DavvagApiManager::$SercurityVault=DavvagApiManager::GetSecurityVault();
        DavvagApiManager::subscribeToEvents();
        require_once (BASE_PATH. "/lib/logger/davvag_logger.php");
        
        try{
            DavvagApiManager::log("api","info","start Invoke");
            DavvagApiManager::$routeManager->loadTenantRoutes();
            DavvagApiManager::log("api","info","end Invoke");
            DavvagLogger::Save(DavvagApiManager::$logObject);
        }catch(Exception $e){
            $err =new stdClass();
            $err->success=false;
            $err->message=$e->getMessage();
            
            DavvagApiManager::log("api","error",$err->message);
            DavvagLogger::Save(DavvagApiManager::$logObject);
            header("content-type: application/json");
            print_r(json_encode($err));
        }

    }


    private static function subscribeToEvents(){
        DavvagApiManager::addAction("end", "DavvagApiManager::subscribeToLogger");
    }

    public static function subscribeToLogger(){
        //remove
    }
    
    public static function GetSecurityVault(){
        $file=BASE_PATH."/SecurityVault/".APIKEY.".json";
        if(file_exists($file)){
            $globaluser =json_decode(file_get_contents($file));
        }
        else{
            die("Unautherized Access.");
        }
        //$globaluser->ApplicationKey="Test";
        $globaluser->UserName="Test";
        return $globaluser;
    }

    public static function addAction($action, $handler){
        DavvagApiManager::$eventManager->addAction($action, $handler);
    }

    public static function addFilter($filter, $handler){
        DavvagApiManager::$eventManager->addFilter($filter, $handler);
    }

    public static function triggerAction($action, $data = null){
        DavvagApiManager::$eventManager->triggerAction($action, $data);
    }

    public static function triggerFilter($filter, $data = null){
        DavvagApiManager::$eventManager->triggerFilter($filter, $data);
    }

    public static function log($app,$logtype,$logstring){
        //echo $logstring;
        $t = microtime(true);
        $micro = sprintf("%06d",($t - floor($t)) * 1000000);
        //echo floor($micro/1000);
        $d = new DateTime( date('Y-m-d H:i:s.'.$micro, $t) );
        
        $time= $d->format("Y-m-d").'T'.$d->format("H:i:s").".".floor($micro/1000);
        //echo $time;
        if(!isset(DavvagApiManager::$logObject)){
            DavvagApiManager::$logObject=new stdClass();
            DavvagApiManager::$logObject->starttime= $time;//date("Y-m-d").'T'.date("H:i:s");
            DavvagApiManager::$logObject->lastexetime= $time;//date("Y-m-d").'T'.date("H:i:s");
            DavvagApiManager::$logObject->apikey=DavvagApiManager::$SercurityVault->ApplicationKey;
            DavvagApiManager::$logObject->path=REQUEST_PATH;
            DavvagApiManager::$logObject->entity=ENTITY;
            DavvagApiManager::$logObject->remoteaddr=IP;
            DavvagApiManager::$logObject->apps = new stdClass();
            //DavvagApiManager::$logObject->
        }
        if(!isset(DavvagApiManager::$logObject->apps->{$app})){
            DavvagApiManager::$logObject->apps->{$app}=new stdClass();
            DavvagApiManager::$logObject->apps->{$app}->apikey=DavvagApiManager::$SercurityVault->ApplicationKey;
            DavvagApiManager::$logObject->apps->{$app}->path=REQUEST_PATH;
            DavvagApiManager::$logObject->apps->{$app}->entity=ENTITY;
            DavvagApiManager::$logObject->apps->{$app}->starttime= $time;//date("Y-m-d").'T'.date("H:i:s");
            DavvagApiManager::$logObject->apps->{$app}->lastexetime= $time;//date("Y-m-d").'T'.date("H:i:s");
            DavvagApiManager::$logObject->apps->{$app}->error=0;
            DavvagApiManager::$logObject->apps->{$app}->info=0;
            DavvagApiManager::$logObject->apps->{$app}->warning=0;
            DavvagApiManager::$logObject->apps->{$app}->other=0;
            DavvagApiManager::$logObject->apps->{$app}->remoteaddr=IP;
            DavvagApiManager::$logObject->apps->{$app}->log=array();
        }
        DavvagApiManager::$logObject->apps->{$app}->lastexetime= $time;//date("Y-m-d").'T'.date("H:i:s");
        DavvagApiManager::$logObject->lastexetime= $time;//date("Y-m-d").'T'.date("H:i:s");
        switch($logtype){
            case "error":
                DavvagApiManager::$logObject->apps->{$app}->error+=1;
            break;
            case "info":
                DavvagApiManager::$logObject->apps->{$app}->info+=1;
                break;
            case "warning":
                DavvagApiManager::$logObject->apps->{$app}->warning+=1;
                break;
            default:
                DavvagApiManager::$logObject->apps->{$app}->other+=1;
                break;
        }
        $logitem =new stdClass();
        $logitem->time= $time;//date("Y-m-d").'T'.date("H:i:s");
        $logitem->type=$logtype;
        $logitem->message=$logstring;
        array_push(DavvagApiManager::$logObject->apps->{$app}->log,$logitem);
        //var_dump()
    }

    
}