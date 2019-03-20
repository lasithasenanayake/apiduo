<?php
define ("BASE_PATH", dirname(__FILE__));
define ("CORE_PATH", BASE_PATH . "/core");
define ("TENANT_PATH", BASE_PATH. "/distributions");


require_once (BASE_PATH . "/davvag_api_manager.php");
DavvagApiManager::start();