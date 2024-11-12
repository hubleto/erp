<?php

namespace CeremonyCrmApp\Modules\Sales\Sales\Models\Eloquent;

use CeremonyCrmApp\Modules\Core\Customers\Models\Eloquent\Activity;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DealActivity extends \ADIOS\Core\Model\Eloquent
{
  public $table = 'deal_activities';

  public function ACTIVITY(): BelongsTo {
    return $this->belongsTo(Activity::class, 'id_activity', 'id');
  }
  public function DEAL(): BelongsTo {
    return $this->belongsTo(Deal::class, 'id_deal', 'id');
  }
}
