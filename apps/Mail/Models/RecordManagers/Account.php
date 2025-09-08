<?php

namespace Hubleto\App\Community\Mail\Models\RecordManagers;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Account extends \Hubleto\Erp\RecordManager
{
  public $table = 'mails_accounts';
}
