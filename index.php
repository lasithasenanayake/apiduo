<?php
define ("BASE_PATH", dirname(__FILE__));
define ("CORE_PATH", BASE_PATH . "/core");
define ("TENANT_PATH", BASE_PATH. "/domains");
define ("TENANT_RESOURCE_PATH", TENANT_PATH . "/$_SERVER[HTTP_HOST]");
define ("UNIT_PATH", BASE_PATH. "/units");

require_once (BASE_PATH . "/davvag_api_manager.php");
DavvagApiManager::start();