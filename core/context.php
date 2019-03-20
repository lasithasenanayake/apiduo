<?php

class Context {

    private $request;
    private $response;
    private $source;

    public function __construct($request,$response) {
        $this->request = $request;
        $this->response = $response;
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

    public function resolve($urn){
        $unit = DavvagApiManager::$resolver->resolve ($view["urn"], $this);
        return $unit;
    }
}