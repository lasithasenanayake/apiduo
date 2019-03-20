<?php

class UrnResolver {
    public function resolve ($urn, $context){

        if ($context->getSource() === null)
            $context->setSource(InvokeSource::REST_API);

        $urnParts = explode(":", $urn);
        $unitType = trim($urnParts[0]);
        $urnInput = trim($urnParts[1]);

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
        }

        if (isset($unit)){
            $unit->setUrnInput($urnInput);
            $unit->setContext($context);
        }

        return $unit;
    }
}