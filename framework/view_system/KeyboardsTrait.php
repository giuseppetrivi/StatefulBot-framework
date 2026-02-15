<?php

namespace StatefulBotFramework\framework\view_system;

use Telegram\Bot\Keyboard\Keyboard;


/**
 * 
 */
trait KeyboardsTrait {
  protected static function createKeyboard($keyboard) {
    return Keyboard::make([
      'keyboard' => $keyboard,
      'resize_keyboard' => true
    ]);
  }
}


?>