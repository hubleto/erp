<?php declare(strict_types=1);

namespace Hubleto\App\Community\AuditLogs\EventListeners;

use Hubleto\App\Community\AuditLogs\Logger;
use Hubleto\Framework\Interfaces\ModelInterface;

class LogCreatedRecord extends \Hubleto\Framework\EventListener implements \Hubleto\Framework\Interfaces\EventListenerInterface
{

  public function onModelAfterCreate(ModelInterface $model, array $record): void
  {
    /** @var Logger */
    $logger = $this->getService(Logger::class);

    try {
      if (isset($model->disableAuditLog) && $model->disableAuditLog) return;
      $logger->logCreate(get_class($this), get_class($model), (int) $record['id']);
    } catch (\Throwable $e) {
      // do nothing
    }
  }

}