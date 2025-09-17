<?php

namespace Hubleto\App\Community\Issues\Models\RecordManagers;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Hubleto\App\Community\Settings\Models\RecordManagers\User;

class Issue extends \Hubleto\Erp\RecordManager
{
  public $table = 'issues';

}
