<?php

namespace HubletoMain\Cli\Agent\App;

class ListInstalled extends \HubletoMain\Cli\Agent\Command
{
  public function run(): void
  {
    $appManager = new \HubletoMain\Core\AppManager($this->main);

    $apps = $appManager->getInstalledApps();

    $this->cli->cyan("You have following apps installed:\n");
    foreach ($apps as $appClass => $appConfig) $this->cli->cyan("  {$appClass}: " . json_encode($appConfig) . "\n");
  }
}