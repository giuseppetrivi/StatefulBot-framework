<?php

namespace FirstPath;

use StatefulBotFramework\control\AbstractState;
use StatefulBotFramework\view\MenuOptions;

class SameNameClass extends AbstractState {

  protected array $valid_static_inputs = [
    MenuOptions::COMMAND_START => "mainCode"
  ];

  protected function mainCode() {
    echo "sono dentro a " . get_class($this);
  }

}

?>