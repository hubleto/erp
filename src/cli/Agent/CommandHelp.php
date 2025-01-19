<?php

namespace HubletoMain\Cli\Agent;

class CommandHelp extends \HubletoMain\Cli\Agent\Command
{
  public function run()
  {
    $this->cli->cyan(file_get_contents(__DIR__ . "/../help.md"));
  }
}