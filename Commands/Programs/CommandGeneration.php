<?php

namespace Commands\Programs;

use Commands\AbstractCommand;

class CommandGeneration extends AbstractCommand {
  protected static ?string $aliasu = 'command-gen';
  protected static bool $requiredCommandValue = true;

  public static function getArguments(): array {
    return [];
  }

  public function execute(): int
  {
    return 0;
  }

}