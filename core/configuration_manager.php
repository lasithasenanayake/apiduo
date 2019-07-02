<?php

class ConfigurationManager {
	/**
         * Developer :Supun  Dissanayake
         * Date : Feb 20 2018
         * Comments: Config Manager 
         * email :supuncodes@gmail.com
         * github : https://github.com/supuncodes
         * company: Duo Software  
         */
    private $globalConfig ;

    public function __construct() {
        
    }

    public function getTenantConfiguration(){
        if (!isset($this->globalConfig)){
            $this->globalConfig = $this->getGlobalConfiguration();
        }

        $tenantConfig = TENANT_RESOURCE_PATH . "/tenant.yml";
        $tenantConfigData = yaml_parse_file($tenantConfig);

        return $this->mergeConfiguration($tenantConfigData);
    }

    public function getGlobalConfiguration(){

    }

    public function getMainConfiguration(){

    }

    private function mergeConfiguration($tenantConfig){
        //inherit global configuration
        return $tenantConfig;
    }


}