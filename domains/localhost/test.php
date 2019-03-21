<?php

return function($context){
    $sqlFactory = $context->resolve ("mssqlfactory");
    $request = $context->getRequest();
    return "Works!!!";
};