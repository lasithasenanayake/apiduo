<?php

require_once   (dirname(__FILE__) .'/vendor/autoload.php');

use Elasticsearch\ClientBuilder;

class DavvagLogger {
    public static function Save($logObject){
        if ($logObject==null)
            return;

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
       
    }
}