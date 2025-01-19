<?php

namespace HubletoMain\Cli\Agent\App;

class ShowInstalled extends \HubletoMain\Cli\Agent\Command
{
  public function run()
  {
    $cli = $this->cli;
    $main = $this->main;

    $installedApps = $main->config['installedApps'];
    ksort($installedApps);

    $cli->cyan("You have following apps installed:\n");
    foreach ($installedApps as $appClass => $appConfig) $cli->cyan("  {$appClass}: " . json_encode($appConfig) . "\n");
  }
}