<?php

namespace HubletoMain\Cli\Agent\App;

class Install extends \HubletoMain\Cli\Agent\Command
{
  public function run(): void
  {
    $appManager = new \HubletoMain\Core\AppManager($this->main);

    $appClass = (string) ($this->arguments[3] ?? '');
    $forceInstall = (bool) ($this->arguments[4] ?? false);

    if (empty($appClass)) {
      $this->cli->red("What app you want to install? Usage: php hubleto app install <APP_NAME>\n");
    }

    require_once($this->main->configAsString('dir', __DIR__) . "/ConfigApp.php");
    require_once($this->main->configAsString('accountDir', __DIR__) . "/ConfigEnv.php");

    $appManager->installApp($appClass, [], $forceInstall);
    $this->cli->green("{$appClass} installed successfully.\n");
  }
}