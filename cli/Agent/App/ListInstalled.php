<?php

namespace HubletoMain\Cli\Agent\App;

class ListInstalled extends \HubletoMain\Cli\Agent\Command
{
  public function run(): void
  {
    $appNamespaces = $this->main->apps->getInstalledAppNamespaces();

    \Hubleto\Terminal::cyan("You have following apps installed:\n");
    foreach ($appNamespaces as $appNamespace => $appConfig) {
      \Hubleto\Terminal::cyan("  {$appNamespace}: " . json_encode($appConfig) . "\n");
    }
  }
}
