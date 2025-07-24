<?php

namespace HubletoApp\Community\Settings\Models\RecordManagers;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Setting extends \Hubleto\Framework\RecordManager
{
  public $table = 'settings';

}
