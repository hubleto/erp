<?php

namespace Hubleto\App\Community\Leads\Models\RecordManagers;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Level extends \Hubleto\Erp\RecordManager
{
  public $table = 'lead_levels';
}
