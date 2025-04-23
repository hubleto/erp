<?php

namespace HubletoMain\Cli\Agent\App;

class Disable extends \HubletoMain\Cli\Agent\Command
{
  public function run(): void
  {
    $appNamespace = (string) ($this->arguments[3] ?? '');

    $this->main->apps->disableApp($appNamespace);
    $this->cli->cyan("{$appNamespace} disabled successfully.\n");
  }
}