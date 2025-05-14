<?php

require_once "Psr4AutoloaderClass.php";

$_Psr4AutoloaderClass = new Psr4AutoloaderClass();

/** namespace prefix */
$_Psr4AutoloaderClass->addNamespace("TGBot", __DIR__); //DA CAMBIARE in BotName

/** register the standard autoloader */
$_Psr4AutoloaderClass->register();


?>