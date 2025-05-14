<?php

namespace TGBot\exceptions\process_exceptions;

use Exception;


class ProcessInputException extends Exception {

  protected const DEFAULT_MESSAGE = "Something went wrong in input processing";

  public function __construct($message=ProcessInputException::DEFAULT_MESSAGE, $code=0, $previous=null) {

    parent::__construct($message, $code, $previous);

  }
  
}

?>