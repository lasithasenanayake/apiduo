<?php

class Context {
/**
         * Developer :Supun  Dissanayake
         * Date : Feb 20 2018
         * Comments: Config Manager 
         * email :supuncodes@gmail.com
         * github : https://github.com/supuncodes
         * company: Duo Software  
         */
    private $request;
    private $response;
    private $source;
    private $viewName;
    private $viewData;

    public function __construct($request,$response,$viewName, $viewData) {
        $this->request = $request;
        $this->response = $response;
        $this->viewName = $viewName;
        $this->viewData =  $viewData;
    }

    public function getRequest(){
        return $this->request;
    }

    public function getResponse(){
        return $this->response;
    }

    public function setSource($source){
        $this->source = $source;
    }

    public function getSource(){
        return $this->source;
    }

    public function getViewName (){
        return $this->viewName;
    }

    public function getViewData(){
        return $this->viewData;
    }

    public function resolve($urn){
        $unit = DavvagApiManager::$resolver->resolve ($urn, $this);
        return $unit;
    }
}