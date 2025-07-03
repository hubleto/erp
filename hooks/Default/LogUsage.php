<?php

namespace HubletoMain\Hook\Default;

class LogUsage extends \HubletoMain\Core\Hook
{

  public function run(string $event, array $args): void
  {
    if ($event == 'controller:init-start') {
      $controller = $args[0];
      if (!$controller->disableLogUsage) {
        $usageApp = $this->main->apps->community('Usage');
        if (is_object($usageApp)) {
          $usageApp->logUsage();
        }
      }
    }
  }

}