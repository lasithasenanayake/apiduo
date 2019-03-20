<?php

class Dispatcher {
    public function dispatch($viewName, $view, $req, $res){
        $context = new Context($req,$res, $viewName, $view);
        $unit = DavvagApiManager::$resolver->resolve ($view["urn"], $context);

        $responseObject = new stdClass();
        
        if (isset($unit)){
            $unit->process(null);

            if ($unit->getSuccess() === true){
                $responseObject->success = true;
                $responseObject->result = $unit->getOutput();
                $res->Set($responseObject);
            }else {
                $responseObject->success = false;
                $responseObject->result = $unit->getOutput();
                $res->SetError($responseObject);
            }
        }else {
            //show error
        }

    }
}

