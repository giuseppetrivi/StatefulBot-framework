<?php

namespace StatefulBotFramework\framework\state_logic;

use StatefulBotFramework\entities\User;
use StatefulBotFramework\exceptions\state_exceptions\StateFunctionException;
use StatefulBotFramework\exceptions\state_exceptions\StateInputException;
use StatefulBotFramework\framework\bot_interface\TGBotInterface;


/**
 * Base class to handle state business logic.
 * 
 * The logic is this:
 * Every state has its pre-conditions, based on the validity of inputs.
 * Inputs can be defined statically or dinamically. Both type of input 
 * has to be verified and determines the procedure execution.
 * 
 * Then there is the core code of the state, that depends on the input.
 * This is the execution of the specific procedure triggered by the (static
 * or dynamic) input.
 * 
 * Finally, there is the only post-condition that is the change of the state
 * in the database. The stat
 * 
 * All this logic is written in `run` method.
 */
abstract class StateBusinessLogic {

  protected TGBotInterface $_Bot;
  protected User $_User;


  /**
   * Array of all valid command-procedure associations.
   * These are defined statically in the specific state class
   * overriding the `defineStaticInputs` method.
   */
  private array $valid_static_inputs = [];


  /**
   * Name and arguments of the function to be called by 
   * the `run()` method.
   */
  private string|null $procedure_to_run = null;
  private array|null $procedure_to_run_args = null;


  /**
   * State name to store in the database the next state
   * of the bot states workflow.
   * By default, this is the actual state path name.
   */
  private string $state_name;

  /**
   * Data to be setted for the next state. 
   * If null, it will be empty.
   */
  private string|null $state_data = null;


  /**
   * Instance of `StatePathManager` class to manipulate
   * the string of the state and navigate through that.
   */
  protected StatePathManager $_StatePath;


  public function __construct(TGBotInterface $_Bot, User $_User) {
    $this->_Bot = $_Bot;
    $this->_User = $_User;
    $this->_StatePath = new StatePathManager(get_class($this));
    $this->state_name = $this->_StatePath->getStatePathName();
  }


  /**
   * Setter method.
   * Set the name of the procedure to run (associated to a static or dynamic command).
   */
  protected function setProcedureToRunByName(string $procedure_name) {
    $this->procedure_to_run = $procedure_name;
  }

  /**
   * Setter method.
   * Set the arguments of the procedure to run (associated to a static or dynamic command).
   * The format of the arguments is `["arg_name1" => val1, "arg_name2" => val2]`.
   */
  protected function setProcedureToRunArgs(array|null $procedure_args) {
    $this->procedure_to_run_args = $procedure_args;
  }


  /**
   * Implement this method to define (by a sequence of `addStaticInput(...)` calls)
   * the command-procedure associations.
   */
  abstract protected function defineStaticInputs();

  /**
   * Method to put a command-procedure association in the `$valid_static_inputs`
   * associative array.
   */
  protected function addStaticInput(string $input_value, string $procedure_name, array|null $procedure_args=null) {
    $this->valid_static_inputs[$input_value] = [
      "procedure_name" => $procedure_name,
      "procedure_args" => $procedure_args
    ];
  }
  
  /**
   * Validate automatically the inputs basing on the valid inputs list.
   * This function is protected because it can be override to make the
   * stantard static validation more custom.
   * This function uses `TGBotInterface` object.
   * 
   * @return bool
   */
  protected function validateStaticInputs(): bool {
    $input_text = $this->_Bot->getInputFromChat()->getText();
    if (count($this->valid_static_inputs)!=0 && array_key_exists($input_text, $this->valid_static_inputs)) {
      $this->setProcedureToRunByName($this->valid_static_inputs[$input_text]["procedure_name"]);
      $this->setProcedureToRunArgs($this->valid_static_inputs[$input_text]["procedure_args"]);
      return true;
    }
    return false;
  }

  /**
   * Validate some dynamic rules, not present in the valid_static_inputs list.
   * It can be overrided and implemented with custom rules.
   * 
   * @return bool
   */
  abstract protected function defineAndValidateDynamicInputs(): bool;


  protected function setNextState($state_name, $state_data=null) {
    $this->state_name = $state_name;
    $this->state_data = $state_data;
  }

  /**
   * Change the state into the database to set it to the next
   * state (eventually also with data).
   */
  private function changeState() {
    $this->_User->getStateHandler()->updateState($this->state_name, $this->state_data);
  }


  /**
   * Verifies the validity of the input of the state, to satify its
   * pre-conditions.
   * This has also to set the specific procedure to be executed in the
   * `executeProcedure()` method.
   * 
   * @return true|Exception
   */
  protected function preConditionInput(): bool|StateInputException|StateFunctionException {
    $this->defineStaticInputs();
    $check_static_inputs = $this->validateStaticInputs();
    if ($check_static_inputs==false) {
      $check_dynamic_inputs = $this->defineAndValidateDynamicInputs();
      if ($check_dynamic_inputs==false) {
        throw new StateInputException("Input doesn't match any of the static and dynamic inputs of this state");
      }
    }

    if ($this->procedure_to_run==null) {
      throw new StateFunctionException("Function to call is null");
    }

    return true;
  }

  /**
   * Contains the core code to execute the actions of the state.
   * There is the standard call to the function setted by the 
   * pre-condition check.
   */
  protected function executeProcedure() {
    $callable = is_string($this->procedure_to_run) 
      ? [$this, $this->procedure_to_run] 
      : $this->procedure_to_run;
    if (is_callable($callable)) {
      if ($this->procedure_to_run_args == null) $callable();
      else $callable(...$this->procedure_to_run_args);
    } 
    else {
      throw new \RuntimeException("Specified function is not callable.");
    }

  }

  /**
   * Verify all the post-conditions, starting from the state change.
   */
  protected function postConditionState() {
    $this->changeState();
  }


  /**
   * Function visible from outside the boundaries of state class
   * that executes the code with pre and post-conditions.
   */
  public function run() {
    //beforePreCondition
    $this->preConditionInput();
    //afterPreCondition
    //beforeProcedure
    $this->executeProcedure();
    //afterProcedure
    //beforePostCondition
    $this->postConditionState();
    //afterPostCondition
  }


}