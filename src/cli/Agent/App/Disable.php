<?php

namespace HubletoMain\Cli\Agent\App;

class Disable extends \HubletoMain\Cli\Agent\Command
{
  public function run(): void
  {
    $appNamespace = (string) ($this->arguments[3] ?? '');

    $appManager = new \HubletoMain\Core\AppManager($this->main);
    $appManager->disableApp($appNamespace);
    $this->cli->cyan("{$appNamespace} disabled successfully.\n");
  }
}