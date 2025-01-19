<?php

namespace HubletoMain\Cli\Agent\App;

class ResetAll extends \HubletoMain\Cli\Agent\Command
{
  public function run()
  {
    $this->cli->cyan("Reinstalling all apps...\n");

    $appManager = new \HubletoMain\Core\AppManager($this->main);
    $appManager->setCli($this->cli);

    require_once(($this->main->config['dir'] ?? __DIR__) . "/ConfigApp.php");
    require_once(($this->main->config['accountDir'] ?? __DIR__) . "/ConfigEnv.php");

    foreach ($this->main->config['apps'] as $appClass => $appConfig) {
      try {
        if (!$appManager->isAppInstalled($appClass)) {
          $appManager->installApp($appClass);
        }
      } catch (\Throwable $e) {
        $this->cli->red($e->getMessage() . "\n");
        $this->cli->red("\n\nThe error was caused by: " . $appClass . "\n");
        $this->cli->red("Verify, whether all your apps have correct dependencies or contact the developers.\n");
      }
    }
  }
}