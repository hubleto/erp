<?php declare(strict_types=1);

namespace Hubleto\App\Community\Usage\EventListeners;

use Hubleto\Erp\Controller;

class LogUsage extends \Hubleto\Framework\EventListener implements \Hubleto\Framework\Interfaces\EventListenerInterface
{

  public function onControllerBeforeInit(Controller $controller): void
  {
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