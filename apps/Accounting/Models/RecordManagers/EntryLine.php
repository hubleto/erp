<?php

namespace Hubleto\App\Community\Accounting\Models\RecordManagers;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EntryLine extends \Hubleto\Erp\RecordManager
{
  public $table = 'accounting_journal_entry_line';

  /** @return BelongsTo */
  public function JOURNAL_ENTRY(): BelongsTo
  {
    return $this->belongsTo(Entry::class, 'id_entry', 'id');
  }

  /** @return BelongsTo */
  public function ACCOUNT(): BelongsTo
  {
    return $this->belongsTo(Account::class, 'id_account', 'id');
  }

  public function prepareReadQuery(mixed $query = null, int $level = 0): mixed
  {
    $query = parent::prepareReadQuery($query, $level);

    $main = \Hubleto\Erp\Loader::getGlobalApp();

    if ($main->router()->urlParamAsInteger("idEntry") > 0) {
      $query = $query->where($this->table . '.id_entry', $main->router()->urlParamAsInteger("idEntry"));
    }

    return $query;
  }

}
