<?php

namespace Hubleto\Erp\Cli\Agent\App;

class ListInstalled extends \Hubleto\Erp\Cli\Agent\Command
{
  public function run(): void
  {
    $appNamespaces = $this->appManager()->getInstalledAppNamespaces();

    \Hubleto\Terminal::cyan("You have following apps installed:\n");
    foreach ($appNamespaces as $appNamespace => $appConfig) {
      \Hubleto\Terminal::cyan("  {$appNamespace}: " . json_encode($appConfig) . "\n");
    }
  }
}
