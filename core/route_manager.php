<?php

require_once (CORE_PATH . "/carbite.php");
require_once (CORE_PATH . "/route_handler.php");
class RouteManager {

    public function loadTenantRoutes(){
        $this->createRoutesInCarbite();
    }

    private function createRoutesInCarbite(){
        $configManager = DavvagApiManager::$configurationManager;
        $tenantConfig = DavvagApiManager::$tenantConfiguration;
        
        if (isset($tenantConfig)){
            if (isset($tenantConfig["views"])){
                
                foreach ($tenantConfig["views"] as $viewName => $view) {
                    $routeHandler = new RouteHandler($viewName, $view);
                    if (isset($view["method"]) && isset($view["route"])){
                        Carbite::HANDLE($view["method"], $view["route"], [$routeHandler,"handle"]);
                    }else {
                        //show error
                        //exit();
                    }
                }
                
                Carbite::AddEvent("completed", function(){
                    DavvagApiManager::triggerAction("end");
                });
               
                Carbite::Start();

            }else {
                //show error
                //exit();
            }
        }else {
            //show error
            //exit();
        }

    }


}