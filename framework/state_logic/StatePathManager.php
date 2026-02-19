<?php

namespace StatefulBotFramework\framework\state_logic;
use StatefulBotFramework\exceptions\state_exceptions\StatePathNavigationException;

class StatePathManager {

  private string $state_path;

  public function __construct(string $classname_complete) {
    $this->state_path = $classname_complete;
  }


  /**
   * From the name of the state, deletes `back_of` names from the last
   * to get the previous state name by the specified level 
   * given in `back_of`
   * 
   * @param int $back_of
   * @return string
   */
  protected function getPreviousState(int $back_of=1): string {
    $array_pf_states = explode("\\", $this->state_path);
    array_splice($array_pf_states, -$back_of);
    $state_result = implode("\\", $array_pf_states);
    if (!isset($state_result) && $state_result==='') {
      throw new StatePathNavigationException();
    }
    return $state_result;
  }


  /**
   * Get the complete state path name
   * 
   * @return string
   */
  public function getStatePathName() {
    return $this->state_path;
  }


  /**
   * Get the last state name of the path
   * 
   * @return string
   */
  public function getStateName() {
    $array_pf_states = explode("\\", $this->state_path);
    $state_name = array_pop($array_pf_states);
    if (!isset($state_name) && $state_name==='') {
      throw new StatePathNavigationException();
    }
    return $state_name;
  }

  /**
   * Append the `next_state` string to the actual state path
   * 
   * @param string $next_state
   * @return string
   */
  public function appendNextState(string $next_state): string {
    return $this->state_path . "\\" . $next_state;
  }



}


?>