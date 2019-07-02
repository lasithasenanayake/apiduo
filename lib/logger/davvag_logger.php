<?php

require_once   (dirname(__FILE__) .'/vendor/autoload.php');

use Elasticsearch\ClientBuilder;

class DavvagLogger {
    /**
         * Developer :Supun  Dissanayake
         * Date : Feb 20 2018
         * Comments: Config Manager 
         * email :supuncodes@gmail.com
         * github : https://github.com/supuncodes
         * company: Duo Software  
         */
    public static function Save($logObject){
        if(!LOGGER){
            return;
        }
        try{
        if ($logObject==null)
            return;
        //echo "In Elastic Search";
        DavvagApiManager::log("elastic_log","info","Start Writing ");
        $client = ClientBuilder::create()->build();
        $paramObj = clone $logObject;
        unset($paramObj->apps);

        foreach ($logObject->apps as $appName => $logData)
        {
            $logData->parameters = $paramObj;
            $params = [
                'index' => $appName,
                'type' => $appName,
                'body' => $logData
            ];

            $response = $client->index($params);
        }
        DavvagApiManager::log("elastic_log","info","end Writing ");
    }catch(Exception $e){
        //echo $e->getMessage();
        DavvagApiManager::log("elastic_log","error",$e->getMessage());
    }
       
    }
}