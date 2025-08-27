<?php

namespace HubletoApp\Community\Issues\Models\RecordManagers;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use HubletoApp\Community\Settings\Models\RecordManagers\User;

class Issue extends \Hubleto\Erp\RecordManager
{
  public $table = 'issues';

}
