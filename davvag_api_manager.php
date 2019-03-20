<?php

require_once (CORE_PATH. "/distribution_manager.php");

class DavvagApiManager {
    

    private $tenantManager;
    private $configurationManager;
    private $mainConfig;

    public function __construct() {
        $this->configurationManager = new ConfigurationManager();
        $this->tenantManager = new DistributionManager();
    }

    public static function start(){
        $this->mainConfig = $this->configurationManager->getMainConfiguration();
    }

}