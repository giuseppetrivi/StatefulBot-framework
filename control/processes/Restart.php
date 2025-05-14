<?php

use TGBot\view;
use TGBot\control\AbstractProcess;
use TGBot\view\Keyboards;


/**
 * 
 */
class Restart extends AbstractProcess {

  protected array $valid_static_inputs = [
    view\MenuOptions::COMMAND_RESTART => "restartProcedure"
  ];


  /**
   * Method to handle the behavior after static input view\MenuOptions::COMMAND_RESTART
   */
  protected function restartProcedure() {
    # Delete all processes into database to restart the procedure from the main menu
    $this->_Bot->sendMessage([
      'text' => "This is the forced restart of the bot...",
      'reply_markup' => Keyboards::getMainMenu()
    ]);
  }


}

?>