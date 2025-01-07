<?php

namespace HubletoApp\Deals\Models\Eloquent;

use HubletoApp\Customers\Models\Eloquent\Activity;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DealActivity extends \ADIOS\Core\Model\Eloquent
{
  public $table = 'deal_activities';

  public function DEAL(): BelongsTo {
    return $this->belongsTo(Deal::class, 'id_deal', 'id');
  }
}
