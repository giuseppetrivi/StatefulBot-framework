<?php

namespace TGBot\entities;


/**
 * Class to handle all attributes of the user
 * The code of this class method depends on your database architecture
 */
class User extends BaseEntity {

  protected int $user_id;
  protected ?ProcessHandler $_ProcessHandler = null;


  /**
   * @param int $user_id Telegram user id
   */
  public function __construct(int $user_id) {
    /** db query to get unique user info */
    
    $this->setUserId($user_id);
    $this->setProcessHandler(new ProcessHandler($this->getUserId()));
  }


  /**
   * [Example of] methods to verify user properties
   */
  public function isActive() {
    return true;
  }
  public function isSubscripted() {
    return true;
  }

}


?>