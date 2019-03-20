<?php

abstract class AbstractUnit {

    private $success;
    private $output;
    private $urnInput;
    private $context;

    public function setUrnInput($urnInput){
        $this->urnInput = $urnInput;
    }

    public function setSuccess($success){
        $this->success = $success;
    }

    public function setOutput($output){
        $this->output = $output;
    }

    public function setContext($context){
        $this->context = $context;
    }

    public function getUrnInput(){
        return $this->urnInput;
    }

    public function getSuccess(){
        return $this->success;
    }

    public function getOutput(){
        return $this->output;
    }

    public function getContext(){
        return $this->context;
    }

    public abstract function process($input);
}