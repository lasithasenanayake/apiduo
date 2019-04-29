<?php

class Autoloader {
    public static function load($className){
        $newName = Autoloader::toDashedCase($class);
        require_once ("./$class.php");
    }

    private static function toDashedCase($input) {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);
        
        $ret = $matches[0];
        
        foreach ($ret as &$match) {
            $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
        }

        return implode('_', $ret);
    }
}