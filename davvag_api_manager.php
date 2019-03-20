<?php

require_once (CORE_PATH. "/configuration_manager.php");
require_once (CORE_PATH. "/route_manager.php");
require_once (CORE_PATH. "/dispatcher.php");
require_once (CORE_PATH. "/urnresolver.php");
require_once (CORE_PATH. "/abstract_unit.php");
require_once (CORE_PATH. "/invoke_source.php");
require_once (CORE_PATH. "/context.php");

class DavvagApiManager {
    
    public static $routeManager;
    public static $configurationManager;
    public static $mainConfig;
    public static $dispatcher;
    public static $resolver;

    public static function start(){
        DavvagApiManager::$configurationManager = new ConfigurationManager();
        DavvagApiManager::$routeManager = new RouteManager();
        DavvagApiManager::$dispatcher = new Dispatcher();
        DavvagApiManager::$resolver = new UrnResolver();
        DavvagApiManager::$mainConfig = DavvagApiManager::$configurationManager->getMainConfiguration();

        DavvagApiManager::$routeManager->loadTenantRoutes();

    }

}