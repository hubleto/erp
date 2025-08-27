<?php

namespace HubletoApp\Community\Documents\Models\RecordManagers;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Folder extends \HubletoMain\RecordManager
{
  public $table = 'folders';

  /** @return BelongsTo<Customer, covariant BillingAccount> */
  public function PARENT_FOLDER(): BelongsTo
  {
    return $this->belongsTo(Folder::class, 'id_parent_folder', 'id');
  }

  public function recordCreate(array $record): array
  {
    if (!isset($record['uid'])) {
      $record['uid'] = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex(random_bytes(16)), 4));
    }
    return parent::recordCreate($record);
  }

}
