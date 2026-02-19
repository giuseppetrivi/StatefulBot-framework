<?php

use StatefulBotFramework\control\StateID;
use StatefulBotFramework\view;
use StatefulBotFramework\framework\state_logic\AbstractState;
use StatefulBotFramework\view\Keyboards;


/**
 * 
 */
class Main extends AbstractState {


  protected function defineStaticInputs() {
    $this->addStaticInput(
      view\MenuOptions::COMMAND_START,
      "startprocedure"
    );
    $this->addStaticInput(
      view\MenuOptions::COMMAND_FIRST_PATH,
      "firstPathProcedure"
    );
    $this->addStaticInput(
      view\MenuOptions::COMMAND_SECOND_PATH,
      "secondPathProcedure"
    );
  }


  /**
   * Method to handle the behavior after static input view\MenuOptions::COMMAND_START
   */
  protected function startProcedure() {
    $this->_Bot->sendMessage([
      'text' => "Hello my friend, this is the start message!",
      'reply_markup' => Keyboards::getMainMenu()
    ]);
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