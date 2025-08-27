<?php

namespace Hubleto\Erp\Cli\Agent\App;

class Disable extends \Hubleto\Erp\Cli\Agent\Command
{
  public function run(): void
  {
    $appNamespace = $this->getAppManager()->sanitizeAppNamespace((string) ($this->arguments[3] ?? ''));

    $this->getAppManager()->disableApp($appNamespace);
    \Hubleto\Terminal::cyan("{$appNamespace} disabled successfully.\n");
  }
}
