<?php

use StatefulBotFramework\control\StateID;
use StatefulBotFramework\view;
use StatefulBotFramework\framework\state_control\AbstractState;
use StatefulBotFramework\view\Keyboards;


/**
 * 
 */
class Main extends AbstractState {

  protected array $valid_static_inputs = [
    view\MenuOptions::COMMAND_START => "startProcedure",
    view\MenuOptions::COMMAND_FIRST_PATH => "firstPathProcedure",
    view\MenuOptions::COMMAND_SECOND_PATH => "secondPathProcedure"
  ];


  /**
   * Method to handle the behavior after static input view\MenuOptions::COMMAND_START
   */
  protected function startProcedure() {
    $this->_Bot->sendMessage([
      'text' => "Hello my friend, this is the start message!",
      'reply_markup' => Keyboards::getMainMenu()
    ]);

    $this->keepActualState();
  }


  protected function firstPathProcedure() {
    $this->_Bot->sendMessage([
      'text' => "First Path started...",
      'reply_markup' => Keyboards::getOnlyBack()
    ]);

    $this->setNextState($this->_StatePath->appendNextState(StateID::FIRST_PATH));
  }

  protected function secondPathProcedure() {
    $this->_Bot->sendMessage([
      'text' => "Second Path started...",
      'reply_markup' => Keyboards::getOnlyBack()
    ]);

    $this->setNextState($this->_StatePath->appendNextState(StateID::SECOND_PATH));
  }


}

?>