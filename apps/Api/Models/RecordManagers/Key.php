<?php

namespace Hubleto\App\Community\Api\Models\RecordManagers;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Key extends \Hubleto\Erp\RecordManager
{
  public $table = 'api_keys';
}
