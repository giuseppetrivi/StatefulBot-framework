<?php

namespace StatefulBotFramework\entities\authorization_rules;

use StatefulBotFramework\config\ConfigurationHandler;
use StatefulBotFramework\framework\rules_system\Rule;

/**
 * Rule to check if the bot is open to users accesses (from config file)
 */
class CheckIfBotIsOpenToAccessRule extends Rule {

  public function __construct(ConfigurationHandler $_Config) {
    parent::__construct($_Config);
  }

  public function rule(): bool {
    $_Config = $this->getValidationClass();
    if ($_Config->getOpenAccessToBot()) {
      return true;
    }
    return false;
  }

}

?>