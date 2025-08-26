<?php

namespace HubletoMain\Cli\Agent\App;

class Disable extends \HubletoMain\Cli\Agent\Command
{
  public function run(): void
  {
    $appNamespace = $this->getAppManager()->sanitizeAppNamespace((string) ($this->arguments[3] ?? ''));

    $this->getAppManager()->disableApp($appNamespace);
    \Hubleto\Terminal::cyan("{$appNamespace} disabled successfully.\n");
  }
}
