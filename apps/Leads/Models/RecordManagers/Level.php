<?php

namespace HubletoApp\Community\Leads\Models\RecordManagers;

use HubletoApp\Community\Settings\Models\RecordManagers\User;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Level extends \Hubleto\Framework\RecordManager
{
  public $table = 'lead_levels';
}
