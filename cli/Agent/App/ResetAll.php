<?php

namespace Hubleto\Erp\Cli\Agent\App;

class ResetAll extends \Hubleto\Erp\Cli\Agent\Command
{
  public function run(): void
  {
    $this->terminal()->cyan("Reinstalling all apps...\n");

    require_once($this->env()->projectFolder . "/ConfigEnv.php");

    foreach ($this->appManager()->getInstalledAppNamespaces() as $appNamespace => $appConfig) {
      try {
        if (!$this->appManager()->isAppInstalled($appNamespace)) {
          $this->appManager()->installApp(1, $appNamespace, []);
        }
      } catch (\Throwable $e) {
        $this->terminal()->red($e->getMessage() . "\n");
        $this->terminal()->red($e->getTraceAsString() . "\n");
        $this->terminal()->red("\n\nThe error was caused by: " . $appNamespace . "\n");
        $this->terminal()->red("Verify, whether all your apps have correct dependencies or contact the developers.\n");
      }
    }
  }
}
