<?php

namespace StatefulBotFramework\framework\bot_interface;

use StatefulBotFramework\framework\BaseEntity;


/**
 * Class to handle all info about the input from chatbot
 */
class InputFromChat extends BaseEntity {

  public $message_type = null;
  public $text = null;


  public function __construct($text, $message_type) {
    $this->text = $text;
    $this->message_type = $message_type;
  }

}

?>