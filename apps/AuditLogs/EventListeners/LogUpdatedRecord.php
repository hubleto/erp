<?php declare(strict_types=1);

namespace Hubleto\App\Community\AuditLogs\EventListeners;

use Hubleto\App\Community\AuditLogs\Logger;
use Hubleto\Framework\Interfaces\ModelInterface;

class LogUpdatedRecord extends \Hubleto\Framework\EventListener implements \Hubleto\Framework\Interfaces\EventListenerInterface
{

  public function onModelAfterUpdate(ModelInterface $model, array $originalRecord, array $savedRecord): void
  {
    /** @var Logger */
    $logger = $this->getService(Logger::class);

    try {
      if (isset($model->disableAuditLog) && $model->disableAuditLog) return;

      $diff = $model->diffRecords($originalRecord, $savedRecord);
      if (count($diff) == 0) return;

      $logger->logUpdate(get_class($this), get_class($model), (int) $savedRecord['id']);
    } catch (\Throwable $e) {
      // do nothing
    }
  }

}