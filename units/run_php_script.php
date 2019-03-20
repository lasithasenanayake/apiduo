<?php

class RunPhpScript extends AbstractUnit {
    public function process ($input){
        $fileName = $this->getUrnInput();
        $fullFilePath =  TENANT_RESOURCE_PATH . "/$fileName";
        $serviceFunction = require_once ($fullFilePath);
        if (isset ($serviceFunction)){
            $context = $this->getContext();
            $output = $serviceFunction($context, $input);
            $this->setSuccess(true);
            $this->setOutput($output);
        }else {
            $this->setSuccess(false);
        }
    }
}