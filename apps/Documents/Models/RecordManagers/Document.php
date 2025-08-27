<?php

namespace HubletoApp\Community\Documents\Models\RecordManagers;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends \HubletoMain\RecordManager
{
  public $table = 'documents';

  /** @return BelongsTo<Customer, covariant BillingAccount> */
  public function FOLDER(): BelongsTo
  {
    return $this->belongsTo(Folder::class, 'id_folder', 'id');
  }

  public function recordCreate(array $record): array
  {
    if (!isset($record['uid'])) {
      $record['uid'] = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex(random_bytes(16)), 4));
    }
    return parent::recordCreate($record);
  }

  public function prepareReadQuery(mixed $query = null, int $level = 0): mixed
  {

    $query = parent::prepareReadQuery($query, $level);

    return $query;
  }

}
