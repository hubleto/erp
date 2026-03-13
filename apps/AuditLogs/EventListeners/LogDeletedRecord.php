<?php declare(strict_types=1);

namespace Hubleto\App\Community\AuditLogs\EventListeners;

use Hubleto\App\Community\AuditLogs\Logger;
use Hubleto\Framework\Interfaces\ModelInterface;

class LogDeletedRecord extends \Hubleto\Framework\EventListener implements \Hubleto\Framework\Interfaces\EventListenerInterface
{

  public function onModelAfterDelete(ModelInterface $model, int $idRecord): void
  {
    /** @var Logger */
    $logger = $this->getService(Logger::class);

    try {
      if (isset($model->disableAuditLog) && $model->disableAuditLog) return;
      $logger->logDelete(get_class($this), get_class($model), $idRecord);
    } catch (\Throwable $e) {
      // do nothing
    }
  }

}