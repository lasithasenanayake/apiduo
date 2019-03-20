<?php

class Resources {
    public static $TENANT_DESCRIPTOR_NOT_FOUND = "Tenant descriptor not found";
    public static $APP_ICON_NOT_FOUND = "Application icon not found";
    public static $APP_DESCRIPTOR_NOT_FOUND = "Application descriptor not found";
    public static $APP_DESCRIPTOR_INVALID_JSON = "Invalid JSON in app descriptor";
    public static $APP_DESCRIPTOR_COMPONENT_NOT_FOUND = "Component not found in app descriptor";
    public static $COMPONENT_DESCRIPTOR_NOT_FOUND = "Component descriptor not found in file system";
    public static $COMPONENT_HANDLER_NOT_FOUND = "No suitable HTTP request handler found in component";
    public static $UNKNOWN_OPERATION = "Unknown Operation";
    public static $COMPONENT_FILE_NOT_FOUND = "File not found in component";
    public static $COMPONENT_SERVICE_HANDLER_NOT_FOUND = "No Service Handler found in component descriptor";
    public static $COMPONENT_SERVICE_HANDLER_FILENOT_FOUND = "File not found for service handler";
    public static $COMPONENT_SERVICE_HANDLER_FILENOT_FOUND_DESCRIPTOR = "File not found for service handler in descriptor";
    public static $COMPONENT_SERVICE_HANDLER_CLASSNOT_FOUND = "class not found for service handler in descriptor";
    public static $COMPONENT_SERVICE_HANDLER_CLASS_NOT_FOUND_PHP = "Class not found in PHP implementation";
    public static $COMPONENT_SERVICE_HANDLER_METHOD_NOT_FOUND_PHP = "Method not found in PHP implementation";

    public static $COMPONENT_TRANSFORMER_NOT_FOUND = "'transformers' section not found ing component desciptor";
    public static $COMPONENT_TRANSFORMER_UNKNOWN = "Unknown transformer uri";
    public static $COMPONENT_UNAUTHORIZED = "Unauthorized";

    public static $APP_STARTUP_CONFIGURED_NOT_INSTALLED = "Startup app is configured but not installed in tenant";
    public static $APP_STARTUP_INCORRECT_CONFIGURATION = "Startup app is incorrectly configured in tenant.json";
    public static $APP_STARTUP_NO_APPS_CONFIGURED = "Startup app is not configured in tenant.json";
    public static $APP_STARTUP_NO_APPS_INSTALLED = "No apps installed on tenant";
    public static $APP_STARTUP_CONFIG_MALFORMED = "Malformed tenant.json";
    
}



?>