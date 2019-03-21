<?php

class EventManager {

    private $filters;
    private $actions;

    public function __construct() {
        $this->filters = array();
        $this->actions = array();
    }

    private function addToArray($array, $action, $handler){
        if (!isset($this->$array[$action])){
            $this->$array[$action] = array();
        }

        array_push($this->$array[$action], $handler);
    }

    public function addAction($action, $handler){
        $this->addToArray("actions", $action, $handler);
    }

    public function addFilter($action, $handler){
        $this->addToArray("filters", $action, $handler);
    }

    private function trigger($array, $action, $data){
        if (isset($this->$array[$action])){
			foreach ($this->$array[$action] as $handler) {
				try{
					$handler($data);
				}catch (Exception $e){

				}
				
			}
        }
    }

    public function triggerAction($action, $data = null){
        $this->trigger("actions", $action, $data);
    }

    public function triggerFilter($action, $data = null){
        $this->trigger("filters", $action, $data);
    }
}