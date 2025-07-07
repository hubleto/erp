<?php

namespace HubletoMain\Cli\Agent\App;

class ResetAll extends \HubletoMain\Cli\Agent\Command
{
  public function run(): void
  {
    $this->cli->cyan("Reinstalling all apps...\n");

    $this->main->apps->setCli($this->cli);

    require_once($this->main->config->getAsString('rootFolder', __DIR__) . "/ConfigEnv.php");

    foreach ($this->main->apps->getInstalledAppNamespaces() as $appNamespace => $appConfig) {
      try {
        if (!$this->main->apps->isAppInstalled($appNamespace)) {
          $this->main->apps->installApp(1, $appNamespace, []);
        }
      } catch (\Throwable $e) {
        $this->cli->red($e->getMessage() . "\n");
        $this->cli->red($e->getTraceAsString() . "\n");
        $this->cli->red("\n\nThe error was caused by: " . $appNamespace . "\n");
        $this->cli->red("Verify, whether all your apps have correct dependencies or contact the developers.\n");
      }
    }
  }
}