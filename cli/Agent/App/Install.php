<?php

namespace Hubleto\Erp\Cli\Agent\App;

class Install extends \Hubleto\Erp\Cli\Agent\Command
{
  public function run(): void
  {
    $appNamespace = $this->appManager()->sanitizeAppNamespace((string) ($this->arguments[3] ?? ''));
    $forceInstall = (bool) ($this->arguments[4] ?? false);

    if (empty($appNamespace)) {
      $this->terminal()->red("What app you want to install? Usage: php hubleto app install <APP_NAME>\n");
    }

    if (!is_file($this->env()->projectFolder . "/ConfigEnv.php")) {
      $this->terminal()->red("ConfigEnv.php file is missing. Run `php hubleto init` first.\n");
      return;
    }

    require_once($this->env()->projectFolder . "/ConfigEnv.php");

    $this->appManager()->installApp(1, $appNamespace, [], $forceInstall);
    $this->terminal()->cyan("{$appNamespace} installed successfully.\n");
  }
}
