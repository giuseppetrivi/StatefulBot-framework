<?php

namespace StatefulBotFramework\view;

/**
 * Final class containing all menu options constants
 */
final class MenuOptions {

  public const COMMAND_START = '/start';
  public const COMMAND_RESTART = '/restart';

  public const COMMAND_FIRST_PATH = '/first_path';
  public const COMMAND_SECOND_PATH = '/second_path';

  public const COMMAND_FINAL_STATE = '/final_state';

  /** ... */

  public const BACK = "← Back";

  
  /**
   * This class is not callable, so constructor is private
   */
  private function __construct() {}

}