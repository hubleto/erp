<?php

namespace Hubleto\App\Community\Accounts\Models\RecordManagers;

use Hubleto\App\Community\Journal\Models\RecordManagers\EntryLine;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Receivable extends \Hubleto\Erp\RecordManager
{
  public $table = 'accounts_receivable';

}
