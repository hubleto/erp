<?php

namespace Hubleto\App\Community\Api\Models\RecordManagers;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Hubleto\App\Community\Settings\Models\RecordManagers\User;
use Hubleto\App\Community\Projects\Models\RecordManagers\Project;

class Key extends \Hubleto\Erp\RecordManager
{
  public $table = 'api_keys';
}
