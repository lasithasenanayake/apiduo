<?php

    class VirtualFirewall {
        public function CheckAuthentication($req,$res){
            $isPermitted = $this->checkPermission($req,$res);
            if ($isPermitted === false){
                $res->Set("Unauthorized");
                exit();
            }            
        }

        private function checkPermission($req,$res){
            return true;
            $appObj = json_decode($_SESSION["appDescriptor"]);
            $compName = $req->Params()->componentName;
            $isAdminComponent = in_array($compName, $appObj->configuration->webdock->firewall->admin);

            if ($isAdminComponent){
                if (!$this->isAdmin()){
                    writeResponse($res, false, Resources::$COMPONENT_UNAUTHORIZED);
                    return false;
                }
            }

            return true;
        }

        private function isAdmin(){
            
            if (isset($_SESSION))
            if (isset($_SESSION["authData"])){
                $authObj = json_decode($_SESSION["authData"]);
                if ($authObj->email === "admin@mylunch.lk")
                    return true;
            }
            return false;
        }
    }

?>