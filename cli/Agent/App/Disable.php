<?php

namespace Hubleto\Erp\Cli\Agent\App;

class Disable extends \Hubleto\Erp\Cli\Agent\Command
{
  public function run(): void
  {
    $appNamespace = $this->appManager()->sanitizeAppNamespace((string) ($this->arguments[3] ?? ''));

    $this->appManager()->disableApp($appNamespace);
    $this->terminal()->cyan("{$appNamespace} disabled successfully.\n");
  }
}
