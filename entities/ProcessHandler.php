<?php

namespace CustomBotName\entities;

use DB;


/**
 * Class to handle process of the user (into the database)
 * The code of this class method depends on your database architecture
 */
class ProcessHandler extends BaseEntity {

  protected int $user_id;


  public function __construct(int $user_id) {
    $this->setUserId($user_id);
  }


  /**
   * 
   */
  public function getProcessName() {
    /** [Example of] db query to get user process info */
    /*
    $result = DB::queryFirstRow("SELECT * FROM [CHANGE THIS: process_table] WHERE [CHANGE THIS: user_id]=%i", $this->getUserId());
    if ($result) { 
      return $result['process_path'];
    }
    else {
      // If there is no process in database, so you need to start the Main process class
      return "Main";
    }
    */
  }


  /**
   * 
   */
  public function createNewProcess() {
    /** db query to insert a new process for the user */
  }

  /**
   * 
   */
  public function updateProcess($new_process_name, $new_process_data=null) {
    /** db query to update user process attributes */
  }

  /**
   * 
   */
  public function updateOnlyProcessData($new_process_data) {
    /** db query to update user process data */
  }

  /**
   * 
   */
  public function deleteProcess() {
    /** db query to delete user process */
  }

}