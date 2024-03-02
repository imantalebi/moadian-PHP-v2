<?php

namespace Imantalebi\MoadianPhpSdk\Exceptions;

class ErrorHandel extends \Exception{
    public function __construct($message, $code = 0, \Throwable $previous = null, $apiReturnedErrors = []) {

        parent::__construct($message, $code, $previous);
    }


}