<?php
class MsSqlFactoryHandler extends AbstractUnit {
    
    public function process ($input){
        $context = $this->getContext();
        return $context->resolve("mssql:$input");
    }

}