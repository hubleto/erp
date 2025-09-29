<?php

namespace Hubleto\App\Community\Accounting\Models\RecordManagers;

use Hubleto\App\Community\Customers\Models\RecordManagers\Customer;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Entry extends \Hubleto\Erp\RecordManager
{
  public $table = 'accounting_journal_entry';

  /** @return hasMany<EntryLine, covariant Entry> */
  public function JOURNAL_ENTRY_LINE(): hasMany
  {
    return $this->hasMany(EntryLine::class, 'id', 'id_entry');
  }
}
