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

    public static function start(){
        DavvagApiManager::$configurationManager = new ConfigurationManager();
        DavvagApiManager::$routeManager = new RouteManager();
        DavvagApiManager::$dispatcher = new Dispatcher();
        DavvagApiManager::$resolver = new UrnResolver();
        DavvagApiManager::$mainConfig = DavvagApiManager::$configurationManager->getMainConfiguration();
        DavvagApiManager::$tenantConfiguration = DavvagApiManager::$configurationManager->getTenantConfiguration();
        DavvagApiManager::$eventManager = new EventManager();

        DavvagApiManager::$routeManager->loadTenantRoutes();

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
}