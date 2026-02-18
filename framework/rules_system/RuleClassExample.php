<?php

namespace StatefulBotFramework\framework\rules_system;


/**
 * [Example of] class rule. Fill the rule() method with the logic you need to check
 */
class RuleClassExample extends Rule {

  /*
  public function __construct(SpecificClass $_ClassInstance) {
    parent::__construct($_ClassInstance);
  }
  */

  public function rule(): bool {
    if (true) {
      return true;
    }
    return false;
  }

}

?>