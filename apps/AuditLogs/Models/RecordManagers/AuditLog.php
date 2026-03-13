<?php

namespace Hubleto\App\Community\AuditLogs\Models\RecordManagers;


use Hubleto\App\Community\Auth\Models\RecordManagers\User;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends \Hubleto\Erp\RecordManager
{
  public $table = 'audit_logs';

  /** @return BelongsTo<User, covariant Customer> */
  public function USER(): BelongsTo
  {
    return $this->belongsTo(User::class, 'id_USER', 'id');
  }

  public function prepareReadQuery(mixed $query = null, int $level = 0, array|null $includeRelations = null): mixed
  {
    $query = parent::prepareReadQuery($query, $level, $includeRelations);
    $hubleto = \Hubleto\Erp\Loader::getGlobalApp();
    return $query;
  }
}
