<?php

namespace HubletoMain\Cli\Agent\App;

class Install extends \HubletoMain\Cli\Agent\Command
{
  public function run(): void
  {
    $appNamespace = $this->main->apps->sanitizeAppNamespace((string) ($this->arguments[3] ?? ''));
    $forceInstall = (bool) ($this->arguments[4] ?? false);

    if (empty($appNamespace)) {
      \Hubleto\Terminal::red("What app you want to install? Usage: php hubleto app install <APP_NAME>\n");
    }

    require_once($this->main->projectFolder . "/ConfigEnv.php");

    $this->main->apps->installApp(1, $appNamespace, [], $forceInstall);
    \Hubleto\Terminal::cyan("{$appNamespace} installed successfully.\n");
  }
}
