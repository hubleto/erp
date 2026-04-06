<?php

namespace Hubleto\App\Community\Documents\Models\RecordManagers;

use Hubleto\App\Community\Auth\Models\RecordManagers\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends \Hubleto\Erp\RecordManager
{
  public $table = 'documents';

  /** @return BelongsTo<Customer, covariant BillingAccount> */
  public function CREATED_BY(): BelongsTo
  {
    return $this->belongsTo(User::class, 'id_created_by', 'id');
  }

  public function prepareReadQuery(mixed $query = null, int $level = 0, array|null $includeRelations = null): mixed
  {

    $query = parent::prepareReadQuery($query, $level, $includeRelations);

    return $query;
  }

}
