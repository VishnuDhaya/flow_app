<?php

namespace App\Exceptions;

use Exception;

class FlowCustomException extends Exception
{
  public $err_code;
  public $response_code;

  public function __construct($message = "", $response_code = 9999, $code = null) {
      $this->err_code = $code;
      $this->response_code = $response_code;

      parent::__construct($message);
  }
}
