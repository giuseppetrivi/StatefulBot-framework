<?php

namespace StatefulBotFramework\exceptions\state_exceptions;

use Exception;


class StatePathNavigationException extends Exception {

  protected const DEFAULT_MESSAGE = "Something went wrong in navigating through the state path";

  public function __construct($message=StatePathNavigationException::DEFAULT_MESSAGE, $code=0, $previous=null) {

    parent::__construct($message, $code, $previous);

  }
  
}

?>