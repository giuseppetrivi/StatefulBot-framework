<?php

namespace SecondPath;

use TGBot\control\AbstractProcess;
use TGBot\view\MenuOptions;

class SameNameClass extends AbstractProcess {

  protected array $valid_static_inputs = [
    MenuOptions::COMMAND_START => "mainCode"
  ];

  protected function mainCode() {
    echo "sono dentro a " . get_class($this);
  }

}

?>