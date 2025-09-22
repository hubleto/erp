<?php

namespace Hubleto\App\Community\Deals\Models\RecordManagers;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Tag extends \Hubleto\Erp\RecordManager
{
  public $table = 'deal_tags';
}
