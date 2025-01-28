<?php

namespace HubletoMain\Cli\Agent\App;

class Disable extends \HubletoMain\Cli\Agent\Command
{
  public function run(): void
  {
    $appClass = (string) ($this->arguments[3] ?? '');

    $appManager = new \HubletoMain\Core\AppManager($this->main);
    $appManager->disableApp($appClass);
    $this->cli->cyan("{$appClass} disabled successfully.\n");
  }
}