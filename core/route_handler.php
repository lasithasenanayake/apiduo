<?php

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