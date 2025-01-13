<?php

namespace HubletoApp\Community\Deals\Models\Eloquent;

use HubletoApp\Community\Customers\Models\Eloquent\Activity;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DealActivity extends \HubletoMain\Core\ModelEloquent
{
  public $table = 'deal_activities';

  public function DEAL(): BelongsTo {
    return $this->belongsTo(Deal::class, 'id_deal', 'id');
  }
}
