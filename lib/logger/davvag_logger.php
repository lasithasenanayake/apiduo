<?php

require_once   (dirname(__FILE__) .'/vendor/autoload.php');

use Elasticsearch\ClientBuilder;

class DavvagLogger {
    public static function Save($logObject){
        if(!LOGGER){
            return;
        }
        try{
        if ($logObject==null)
            return;
        //echo "In Elastic Search";
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
    }catch(Exception $e){
        //echo $e->getMessage();
    }
       
    }
}