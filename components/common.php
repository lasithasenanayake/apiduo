<?php

    function writeResponse($res, $success, $result){
        $sObj =new stdClass();
        $sObj->success = $success;
        $sObj->result = $result;
        $res->Set($sObj);
    }



    function sendRestRequest($url, $method, $body = null, $forwardHeaders = null){
        $ch=curl_init();
        
        //$currentHeaders = apache_request_headers();
        if (isset($forwardHeaders)){
            array_push($forwardHeaders, "Host: $_SERVER[HTTP_HOST]");
            array_push($forwardHeaders, "Content-Type: application/json");
            curl_setopt($ch, CURLOPT_HTTPHEADER, $forwardHeaders);
        }
        /*
        foreach ($currentHeaders as $key => $value)
            if (!(strcmp(strtolower($key), "host") ===0 || strcmp(strtolower($key),"content-type")===0))
                array_push($forwardHeaders, "$key : $value");
        */
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

        if(isset($body)){            
            curl_setopt($ch, CURLOPT_POST, count($body));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);    
        }

        $data = curl_exec($ch);

        return $data;
    }
?>