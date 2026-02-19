<?php

namespace StatefulBotFramework\framework\state_logic;


/**
 * Base class to handle states.
 * 
 * The logic is this:
 * Every state has its pre-conditions, based on the validity of inputs.
 * Inputs can be verified statically or dinamically.
 * 
 * Then there is the core code of the state, that depends on the input
 * 
 * Finally, there is the only post-condition that is the change of the state
 * in the database
 */
abstract class AbstractState extends StateBusinessLogic {
  

  protected function defineStaticInputs() {
    return;
  }

  protected function defineAndValidateDynamicInputs(): bool {
    return false;
  }


  /**
   * Empty procedure to simply not do anything
   */
  protected function emptyProcedure() {
    // TODO da eliminare probabilmente
  }


  /**
   * Simple test function
   */
  public function testExecution() {
    return "I'm into the class " . get_class($this);
  }


}