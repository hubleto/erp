<?php

namespace HubletoMain\Cli\Agent\App;

class Disable extends \HubletoMain\Cli\Agent\Command
{
  public function run(): void
  {
    $appNamespace = $this->sanitizeAppNamespace((string) ($this->arguments[3] ?? ''));

    $this->main->apps->disableApp($appNamespace);
    \Hubleto\Terminal::cyan("{$appNamespace} disabled successfully.\n");
  }
}
