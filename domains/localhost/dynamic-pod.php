<?php
        /**
         * Developer :Lasitha Senanayake
         * Date : Jun 10 2019
         * Comments: Dynamic Query
         * email :lasitha.senanayake@gmail.com
         * github : https://github.com/lasithasenanayake
         * company: Duo Software  
         */
    return function($context){
        $request = $context->getRequest();
        $file=BASE_PATH."/query-pods/".$request->Params()->query_file.".json";
        $queryObj =json_decode(file_get_contents($file));
        $cacheid=$request->Params()->query_file;
        foreach ($queryObj->parameters as  $value) {

            if(isset($_GET[$value])){
                $queryObj->sql=str_replace('@'.$value,$_GET[$value],$queryObj->sql);
                $cacheid.='-'.$_GET[$value];
            }else{
                throw new Exception("Parameters are not provided"); 
            }
            
            
        }

        if($queryObj->cache){
            
            $cacheObj=CacheData::getObjects($cacheid,"query_pods",$queryObj->cachehours);
            if($cacheObj){
                  return $cacheObj;
            }
        }
        if(isset($queryObj->entity)){
            $_SESSION["ENTITY"]=$queryObj->entity;
        }
        $sqlUnit = $context->resolve("mssql:query");
        $results=$sqlUnit->process($queryObj->sql);
        if($queryObj->cache){
            CacheData::setObjects($cacheid,"query_pods",$results);
        }
        return $results;
    };
?>