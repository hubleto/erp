<?php declare(strict_types=1);

namespace Hubleto\App\Community\Usage\Hooks;

class LogUsage extends \Hubleto\Erp\Hook
{

  public function run(string $event, array $args): void
  {
    if ($event == 'controller:init-start') {
      $controller = $args[0];
      if (!$controller->disableLogUsage) {
        try {
          $usageLogger = $this->getService(\Hubleto\App\Community\Usage\Logger::class);
          $usageLogger->logUsage();
        } catch (\Throwable $e) {
          //
        }
      }
    }
  }

}