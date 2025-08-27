<?php declare(strict_types=1);

namespace HubletoApp\Community\Usage\Hooks;

class LogUsage extends \HubletoMain\Hook
{

  public function run(string $event, array $args): void
  {
    if ($event == 'controller:init-start') {
      $controller = $args[0];
      if (!$controller->disableLogUsage) {
        try {
          $usageLogger = $this->getService(\HubletoApp\Community\Usage\Logger::class);
          $usageLogger->logUsage();
        } catch (\Throwable $e) {
          //
        }
      }
    }
  }

}