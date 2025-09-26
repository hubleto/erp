<?php

namespace Hubleto\App\Community\BkTransactions\Models\RecordManagers;

use Hubleto\App\Community\BkJournal\Models\RecordManagers\EntryLine;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reconciliation extends \Hubleto\Erp\RecordManager
{
  public $table = 'transactions_reconciliation';

  /** @return BelongsTo<Transaction, covariant Reconciliation> */
  public function TRANSACTION(): BelongsTo
  {
    return $this->belongsTo(Transaction::class, 'id_transaction', 'id');
  }

  /** @return BelongsTo<EntryLine, covariant Reconciliation> */
  public function JOURNAL_ENTRY_LINE(): BelongsTo
  {
    return $this->belongsTo(EntryLine::class, 'id_journal_entry_line', 'id');
  }
}
