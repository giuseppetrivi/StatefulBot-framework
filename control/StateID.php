<?php

namespace StatefulBotFramework\control;

/**
 * Readonly class where the names of the states are stored, to make
 * them callable by name.
 */
readonly class StateID {

  public const MAIN = "Main";
  public const FIRST_PATH = "FirstPath";
  public const SECOND_PATH = "SecondPath";
  public const FINAL_STATE = "FinalState";

}



?>