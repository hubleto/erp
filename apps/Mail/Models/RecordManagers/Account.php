<?php

namespace HubletoApp\Community\Mail\Models\RecordManagers;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Account extends \HubletoMain\RecordManager
{
  public $table = 'mails_accounts';
}
