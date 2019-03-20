<?php

class ConfigurationManager {

    private $globalConfig;


    public function getTenantConfiguration($tenant){

    }

    public function getGlobalConfiguration(){

    }

    public function getMainConfiguration(){
        if (!isset($this->$globalConfig)){
            $this->globalConfig = $this->getGlobalConfiguration();
        }
    }

    private function mergeConfiguration(){

    }


}