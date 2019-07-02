<?php
/**
         * Developer :Supun  Dissanayake
         * Date : Feb 20 2018
         * Comments: Config Manager 
         * email :supuncodes@gmail.com
         * github : https://github.com/supuncodes
         * company: Duo Software  
         */
class RouteHandler {

    private $view;
    private $viewName;

    public function __construct($viewName, $view) {
        $this->viewName = $viewName;
        $this->view = $view;    
    }

    public function handle($req,$res){
        DavvagApiManager::$dispatcher->dispatch($this->viewName, $this->view, $req, $res);
    }

}