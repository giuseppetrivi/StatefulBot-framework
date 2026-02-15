<?php

namespace StatefulBotFramework\entities\authorization_rules;

use StatefulBotFramework\entities\User;


/**
 * Rule to check if the user is active to bot (from user info)
 */
class CheckIfUserIsActiveRule extends Rule {

  public function __construct(User $_User) {
    parent::__construct($_User);
  }

  public function rule(): bool {
    $_User = $this->getValidationClass();
    if ($_User->isActive()) {
      return true;
    }
    return false;
  }

}

?>