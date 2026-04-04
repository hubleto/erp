<?php

namespace Hubleto\App\Community\Documents\Models\RecordManagers;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class File extends \Hubleto\Erp\RecordManager
{
  public $table = 'files';

  /** @return BelongsTo<Customer, covariant BillingAccount> */
  public function FOLDER(): BelongsTo
  {
    return $this->belongsTo(Folder::class, 'id_folder', 'id');
  }

  public function prepareReadQuery(mixed $query = null, int $level = 0, array|null $includeRelations = null): mixed
  {

    $query = parent::prepareReadQuery($query, $level, $includeRelations);

    return $query;
  }

}
