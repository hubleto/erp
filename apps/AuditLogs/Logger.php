<?php

namespace Hubleto\App\Community\AuditLogs;

use Hubleto\Erp\Core;

class Logger extends Core
{

  /**
   * [Description for log]
   *
   * @param int $category
   * @param array $tags
   * @param int $idTo
   * @param string $subject
   * @param string $body
   * @param string $color
   * @param int $priority
   * 
   * @return array
   * 
   */
  public function log(
    int $type, // Create, Update, Delete
    string $context, // e.g. class of the controller where the log was created
    string $model, 
    int $recordId,
    string $message,
    int $priority = 0 // 0 = lowest, higher number = higher priority
  ): void {
    try {
      $mAuditLog = $this->getModel(Models\AuditLog::class);
      $mAuditLog->record->create([
        'datetime' => date("Y-m-d H:i:s"),
        'type' => $type,
        'context' => $context,
        'model' => $model,
        'record_id' => $recordId,
        'message' => $message,
        'priority' => $priority,
        'id_user' => $this->authProvider()->getUserId(),
        'ip' => $_SERVER['REMOTE_ADDR'] ?? '',
      ]);
    } catch (\Throwable $e) {
      // do nothing
    }
  }

  /**
   * [Description for logCreate]
   *
   * @param string $context
   * @param string $model
   * @param string $message
   * @param int $priority
   * 
   * @return void
   * 
   */
  public function logCreate(string $context, string $model, int $recordId = 0, string $message = '', int $priority = 0): void
  {
    $this->log(Models\AuditLog::TYPE_CREATE, $context, $model, $recordId, $message, $priority);
  }

  /**
   * [Description for logUpdate]
   *
   * @param string $context
   * @param string $model
   * @param string $message
   * @param int $priority
   * 
   * @return void
   * 
   */
  public function logUpdate(string $context, string $model, int $recordId = 0, string $message = '', int $priority = 0): void
  {
    $this->log(Models\AuditLog::TYPE_UPDATE, $context, $model, $recordId, $message, $priority);
  }

  /**
   * [Description for logDelete]
   *
   * @param string $context
   * @param string $model
   * @param string $message
   * @param int $priority
   * 
   * @return void
   * 
   */
  public function logDelete(string $context, string $model, int $recordId = 0, string $message = '', int $priority = 0): void
  {
    $this->log(Models\AuditLog::TYPE_DELETE, $context, $model, $recordId, $message, $priority);
  }

}
