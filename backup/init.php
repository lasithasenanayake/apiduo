<?php
    require_once (dirname(__FILE__) . "/components/resources.php");
    
    class SOSSPlatform {
        static function intialize(){
            $tenantFile = TENANT_RESOURCE_LOCATION . "/tenant.json";
            $fileToServe;$errorMsg;

            if (file_exists($tenantFile)){
                $jsonContents = file_get_contents($tenantFile);
                $tenantObj = json_decode($jsonContents);
    
                if (isset($tenantObj)){
                    if (isset($tenantObj->apps)){
                        $startupApp;                        
                        if (isset($tenantObj->webdock)){
                            
                            if (isset($tenantObj->webdock->events)){
                                $startupApp = $tenantObj->webdock->events->onStartup;
                            }
                        }

                        if (isset($startupApp)){
                            if (is_object($startupApp)){
                                $isAdmin = false;
                                if (defined("IS_ADMIN_MODE")){
                                    $isAdmin = IS_ADMIN_MODE;
                                }
                                
                                $startupApp =  $isAdmin ?  $startupApp->admin : $startupApp->default;
                            }
                            
                            if (is_string($startupApp)){
                                if (!isset($tenantObj->apps->$startupApp)){
                                    $errorMsg = RESOURCES::$APP_STARTUP_CONFIGURED_NOT_INSTALLED;
                                }else {
                                    $fileToServe = TENANT_RESOURCE_LOCATION . "/$startupApp/app.php";
                                }
                            }else {
                                $errorMsg = RESOURCES::$APP_STARTUP_INCORRECT_CONFIGURATION;        
                            }

                        }else {
                            $errorMsg = RESOURCES::$APP_STARTUP_NO_APPS_CONFIGURED;
                        }
                    }else {
                        $errorMsg = RESOURCES::$APP_STARTUP_NO_APPS_INSTALLED;
                    }
                }else {
                    $errorMsg = RESOURCES::$APP_STARTUP_CONFIG_MALFORMED;
                }
            }else {
                //echo RESOURCE_LOCATION."/www.".HOST_NAME;
                if(is_dir(RESOURCE_LOCATION."/www.".HOST_NAME)){
                    header("Location: http://www.".HOST_NAME);
                    die();
                }
                $errorMsg = "$tenantFile not found";
            }
            
            if (isset($fileToServe)){
                require_once ($fileToServe);
            }else {
                echo $errorMsg;
            }
        }
    }
?>