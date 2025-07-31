<?php

namespace HubletoMain\Cli\Agent\App;

class ResetAll extends \HubletoMain\Cli\Agent\Command
{
  public function run(): void
  {
    \Hubleto\Terminal::cyan("Reinstalling all apps...\n");

    require_once($this->main->projectFolder . "/ConfigEnv.php");

    foreach ($this->main->apps->getInstalledAppNamespaces() as $appNamespace => $appConfig) {
      try {
        if (!$this->main->apps->isAppInstalled($appNamespace)) {
          $this->main->apps->installApp(1, $appNamespace, []);
        }
      } catch (\Throwable $e) {
        \Hubleto\Terminal::red($e->getMessage() . "\n");
        \Hubleto\Terminal::red($e->getTraceAsString() . "\n");
        \Hubleto\Terminal::red("\n\nThe error was caused by: " . $appNamespace . "\n");
        \Hubleto\Terminal::red("Verify, whether all your apps have correct dependencies or contact the developers.\n");
      }
    }
  }
}
