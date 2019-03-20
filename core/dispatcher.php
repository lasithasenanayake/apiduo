<?php

class Dispatcher {
    public function dispatch($viewName, $view, $req, $res){
        $context = new Context($req,$res);
        $unit = DavvagApiManager::$resolver->resolve ($view["urn"], $context);

        if (isset($unit)){
            $result = $unit->process($view);

            if ($unit->getSuccess() === true){
    
            }else {
    
            }
        }else {
            //show error
        }

    }
}

