<?php

namespace TGBot\entities;

use TGBot\exceptions\RuleException;
use TGBot\entities\authorization_rules\Rule;
use TGBot\entities\authorization_rules\CheckIfUserIsActiveRule;
use TGBot\entities\authorization_rules\CheckIfUserIsSubscriptedRule;


/**
 * Class that executes preliminary checks on the User
 */
class UserAuthorization extends BaseEntity {

  public array $rules = [];

  protected ?User $_User = null;


  public function __construct(User $_User) {
    $this->setUser($_User);

    $this->rulesToAdd();
  }


  /**
   * Adds the specified rule into $rules array 
   * 
   * @param Rule $_Rule Specified rule object
   */
  private function addRule(Rule $_Rule) {
    array_push($this->rules, $_Rule);
  }

  /**
   * Choose which specific rule objects add into $rules array
   * [This rules are basic examples]
   */
  private function rulesToAdd() {
    $this->addRule(new CheckIfUserIsActiveRule($this->getUser()));
    $this->addRule(new CheckIfUserIsSubscriptedRule($this->getUser()));
  }


  /**
   * Checks every rule into $rules array
   * 
   * @return void|RuleException Exception when a rule is not fulfilled by the user, with the specific rule error message
   */
  public function verifyAuthorization() {
    $rules = $this->getRules();
    foreach ($rules as $_SpecificRuleInstance) {
      if (!$_SpecificRuleInstance->rule()) {
        throw new RuleException($_SpecificRuleInstance->errorMessage());
        break;
      }
    }
  }

}

?>
