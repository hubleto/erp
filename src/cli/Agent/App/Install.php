<?php

namespace HubletoMain\Cli\Agent\App;

class Install extends \HubletoMain\Cli\Agent\Command
{
  public function run(): void
  {
    $cli = $this->cli;

    $appManager = new \HubletoMain\Core\AppManager($this->main);

    $appClass = $this->arguments[3] ?? '';
    $forceInstall = (bool) ($this->arguments[4] ?? false);

    if (empty($appClass)) {
      $cli->red("What app you want to install? Usage: php hubleto app install <APP_NAME>\n");
    }

    require_once(($this->main->config['dir'] ?? __DIR__) . "/ConfigApp.php");
    require_once(($this->main->config['accountDir'] ?? __DIR__) . "/ConfigEnv.php");

    $appManager->installApp($appClass, $forceInstall);
    $cli->green("{$appClass} installed successfully.\n");
  }
}