<?php

namespace StatefulBotFramework\init;

use DB;
use Exception;
use StatefulBotFramework\config\ConfigurationHandler;
use StatefulBotFramework\framework\bot_interface\TGBotInterface;
use StatefulBotFramework\entities\User;
use StatefulBotFramework\entities\BotAuthorization;
use StatefulBotFramework\entities\UserAuthorization;
use Restart;
use StatefulBotFramework\exceptions\state_exceptions\StateInputException;
use StatefulBotFramework\control\StateID;
use Telegram\Bot\Exceptions\TelegramBotNotFoundException;

class Init {

  private static ConfigurationHandler $_Config;
  private static TGBotInterface $_Bot;
  private static User $_User;


  /**
   * Static method to perform all the steps necessary to process the bot's request
   */
  public static function initRequestProcessing(string $development_mode) {
    self::initializeConfiguration($development_mode);
    self::initializeDatabase();
    self::initializeBotSdk();
    self::initializeUser();

    self::checkBotAuthorization();
    self::checkUserAuthorization();

    self::handleRestartCommand();
    self::handleCommand();
  }


  /**
   * @param string $development_mode Mode to take right configuration properties (for example production, testing, ...)
   */
  private static function initializeConfiguration(string $development_mode) {
    self::$_Config = ConfigurationHandler::setInstance($development_mode);
  }

  /**
   * Database initialization using DB static class from meekro
   */
  private static function initializeDatabase() {
    DB::$user = self::$_Config->getDatabaseUsername();
    DB::$password = self::$_Config->getDatabasePassword();
    DB::$dbName = self::$_Config->getDatabaseName();
  }

  /**
   * TelergamBotSdk initialization by custom interface
   */
  private static function initializeBotSdk() {
    try {
      $telegram_bot_api_token = self::$_Config->getTelegramBotApiToken();
      self::$_Bot = new TGBotInterface($telegram_bot_api_token);
    } catch(Exception $e) {
      print($e->getMessage());
      throw new TelegramBotNotFoundException("Something went wrong in Bot instance initialization.");
    }
  }

  /**
   * User instance initialization, thanks to Bot instance informations
   */
  private static function initializeUser() {
    $user_id = self::$_Bot->getChatId();
    self::$_User = new User($user_id);
  }


  /**
   * [Example of] preliminary authorization of bot rules
   */
  private static function checkBotAuthorization() {
    try {
      $_BotAuthorization = new BotAuthorization(self::$_Config);
      $_BotAuthorization->verifyAuthorization();
      self::$_Bot->sendMessage([
        'text' => "Bot authorization check gone well..."
      ]);
    } catch(Exception $e) {
      self::$_Bot->sendMessage([
        'text' => "Bot doesn't authorize access: " . $e->getMessage()
      ]);
      exit;
    }
  }

  /**
   * [Example of] preliminary authorization of user rules
   */
  private static function checkUserAuthorization() {
    try {
      $_UserAuthorization = new UserAuthorization(self::$_User);
      $_UserAuthorization->verifyAuthorization();
      self::$_Bot->sendMessage([
        'text' => "User authorization check gone well..."
      ]);
    } catch(Exception $e) {
      self::$_Bot->sendMessage([
        'text' => "User is not authorized: " . $e->getMessage()
      ]);
      exit;
    }
  }


  /**
   * Handles the priority of a restart command (for example /restartbot, to take
   * back to main menu and reset database instances...)
   */
  private static function handleRestartCommand() {
    try {
      $_State = new Restart(self::$_Bot, self::$_User);
      $_State->run();
      exit;
    } catch(StateInputException $e) {
      /* command is not a restart command */
    }
  }

  /**
   * Handles the states called by commands
   */
  private static function handleCommand() {
    $state_name = self::$_User->getStateHandler()->getStateName();
    $state_name = StateID::MAIN; // TODO to delete
    try {
      $_State = new $state_name(self::$_Bot, self::$_User);
      $_State->run();
    } catch(StateInputException $e) {
      self::$_Bot->sendMessage([
        'text' => $e->getMessage()
      ]);
    }
  }

}