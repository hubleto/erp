<?php

namespace HubletoApp\Community\Deals\Models\RecordManagers;

use HubletoApp\Community\Settings\Models\RecordManagers\User;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Tag extends \Hubleto\Framework\RecordManager
{
  public $table = 'deal_tags';
}
