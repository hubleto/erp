<?php

namespace Hubleto\App\Community\BkAccounts\Models\RecordManagers;

use Hubleto\App\Community\BkJournal\Models\RecordManagers\EntryLine;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Receivable extends \Hubleto\Erp\RecordManager
{
  public $table = 'accounts_receivable';

}
