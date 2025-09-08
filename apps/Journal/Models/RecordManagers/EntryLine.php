<?php

namespace Hubleto\App\Community\Journal\Models\RecordManagers;

use Hubleto\App\Community\ChartOfAccounts\Models\RecordManagers\Account;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EntryLine extends \Hubleto\Erp\RecordManager
{
  public $table = 'journal_entry_line';

  /** @return BelongsTo */
  public function JOURNAL_ENTRY(): BelongsTo
  {
    return $this->belongsTo(Entry::class, 'id', 'id_entry');
  }

  /** @return BelongsTo */
  public function ACCOUNT(): BelongsTo
  {
    return $this->belongsTo(Account::class, 'id', 'id_account');
  }

}
