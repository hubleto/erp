<?php

namespace Hubleto\App\Community\Settings\Models\RecordManagers;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Currency extends \Hubleto\Erp\RecordManager
{
  public $table = 'currencies';
}
