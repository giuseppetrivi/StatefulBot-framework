<?php

namespace CustomBotName\control;

use CustomBotName\exceptions\process_exceptions\ProcessFunctionException;
use CustomBotName\exceptions\process_exceptions\ProcessInputException;

/**
 * Base class to handle processes.
 * 
 * The logic is this:
 * Every process has its pre-conditions, based on the validity of inputs.
 * Inputs can be verified statically or dinamically.
 * 
 * Then there is the core code of the process, that depends on the input
 * 
 * Finally, there is the only post-condition that is the change of the status
 * in the database row for the process
 */
abstract class AbstractProcess {
  protected $_Bot;
  protected $_User;

  /**
   * Array of all valid inputs possibile for the current specific
   * process
   * [SHOULD BE OVERRIDDEN]
   */
  protected array $valid_static_inputs = [];

  /**
   * Name of the function to be called by the mainCode method
   */
  protected string|null $function_to_call = null;

  /**
   * Next process to be setted in the database at the end of all
   * the core code of the current process. If null it will delete
   * the record process in the database. This means that the next
   * process will be the start menu
   */
  protected string|null $process_name = null;
  /**
   * Data to be setted fo the next process. If null, it will be empty
   */
  protected string|null $process_data = null;

  /**
   * @param TelegramBotSdkCustomInterface $_Bot
   * @param User $_User
   */
  public function __construct($_Bot, $_User) {
    $this->_Bot = $_Bot;
    $this->_User = $_User;
  }
  

  /**
   * Validate automatically the inputs basing on the valid_inputs list.
   * This function is protected because it can be override to make the
   * stantard static validation more custom.
   * This function uses TelegramBotSdkCustomInterface object.
   * 
   * @return bool
   */
  protected function validateStaticInputs() {
    $input_text = $this->_Bot->getInputFromChat()->getText();
    if (count($this->valid_static_inputs)!=0 && array_key_exists($input_text, $this->valid_static_inputs)) {
      $this->function_to_call = $this->valid_static_inputs[$input_text];
      return true;
    }
    return false;
  }

  /**
   * Validate some dynamic rules, not present in the valid_static_inputs list.
   * The default return is true, but can be overrided and implemented
   * pesonally
   * [CAN OVERRIDE, for personal rules]
   * 
   * @return bool
   */
  protected function validateDynamicInputs() {
    return false;
  }


  /**
   * From the name of the process, deletes the last one to get
   * the previous process name
   * 
   * @return array
   */
  protected function getPreviousProcess() {
    $classname_complete = get_class($this);

    $array_pf_processes = explode("\\", $classname_complete);
    array_pop($array_pf_processes);
    return implode("\\", $array_pf_processes);
  }

  /**
   * Append the parameter "next_process" to the actual process
   * 
   * @param string $next_process
   * @return string
   */
  protected function appendNextProcess($next_process) {
    return get_class($this) . "\\" . $next_process;
  }

  /**
   * Set the next_process attribute
   */
  protected function setNextProcess($process_name=null, $process_data=null) {
    $this->process_name = $process_name;
    $this->process_data = $process_data;
  }

  /**
   * Change the process into the database to set it to the next
   * process (eventually also with data)
   */
  private function changeProcess() {
    $this->_User->getProcessHandler()->updateProcess($this->process_name);
    $this->_User->getProcessHandler()->updateProcess($this->process_data);
  }



  /**
   * Verifies the validity of the input of the process, to satify its
   * pre-conditions
   * This has also to set the specific procedure to be executed in the
   * mainCode block
   * 
   * @return true|Exception
   */
  protected function preConditionInput() {
    $check_static_inputs = $this->validateStaticInputs();
    if ($check_static_inputs==false) {
      $check_dynamic_inputs = $this->validateDynamicInputs();
      if ($check_dynamic_inputs==false) {
        throw new ProcessInputException("Input doesn't match any of the static and dynamic inputs of this process");
      }
    }

    if ($this->function_to_call==null) {
      throw new ProcessFunctionException("Function to call is null");
    }

    return true;
  }

  /**
   * Contains the core code to execute the actions of the process.
   * There is the standard call to the function to execute setted
   * by the pre-condition check
   */
  protected function mainCode() {
    call_user_func(array($this, $this->function_to_call));
  }

  /**
   * Verify all the post-conditions, starting from the
   * change of the process
   */
  protected function postConditionProcess() {
    $this->changeProcess();
  }

  /**
   * Function visible from outside the boundaries of Process class
   * that executes the code with pre and post-conditions
   */
  public function codeToRun() {
    $this->preConditionInput();
    $this->mainCode();
    $this->postConditionProcess();
  }


  /**
   * Simple test function
   */
  public function testExecution() {
    return "I'm into the class " . get_class($this);
  }


}