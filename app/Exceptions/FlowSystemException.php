<?php

namespace App\Exceptions;
use Exception;

class FlowSystemException extends Exception
{
  public $err_code;
  public function __construct($message = "", $http_code = null) {
      $this->err_code = $http_code;
      parent::__construct($message);
  }
}
