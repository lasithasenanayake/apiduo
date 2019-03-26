<?php

class UrnResolver {
    public function resolve ($urn, $context){

        if ($context->getSource() === null)
            $context->setSource(0);

        $urnParts = explode(":", $urn);
        $unitType = trim($urnParts[0]);
        $urnInput = sizeof($urnParts) == 1 ? null : trim($urnParts[1]);

        $unit;
        switch ($unitType) {
            case "file":
                require_once(UNIT_PATH . "/run_php_script.php");
                $unit = new RunPhpScript();
                break;
            case "mssql":
                require_once(UNIT_PATH . "/mssql/mssql_handler.php");
                $unit = new MsSqlHandler();
                break;
            case "mssqlfactory":
                require_once(UNIT_PATH . "/mssql/mssql_factory.php");
                $unit = new MsSqlFactoryHandler();
                break;
        }

        if (isset($unit)){
            $unit->setUrnInput($urnInput);
            $unit->setContext($context);
        }

        return $unit;
    }
}