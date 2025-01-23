<?php

namespace HubletoMain\Cli\Agent\App;

class Disable extends \HubletoMain\Cli\Agent\Command
{
  public function run(): void
  {
    $appManager = new \HubletoMain\Core\AppManager($this->main);

    $appClass = $this->arguments[3] ?? '';
    $appManager->disableApp($appClass);
    $this->cli->cyan("{$appClass} disabled successfully.\n");
  }
}