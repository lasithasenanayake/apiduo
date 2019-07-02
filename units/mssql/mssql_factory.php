<?php
class MsSqlFactoryHandler extends AbstractUnit {
     /**
         * Developer :Lasitha Senanayake
         * Date : May 1 2019
         * Comments: MS SQL Process Handler
         * email :lasitha.senanayake@gmail.com
         * github : https://github.com/lasithasenanayake
         * company: Duo Software  
         */
    public function process ($input){
        $context = $this->getContext();
        return $context->resolve("mssql:$input");
    }

}