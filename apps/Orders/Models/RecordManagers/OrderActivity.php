<?php

namespace Hubleto\App\Community\Orders\Models\RecordManagers;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderActivity extends \Hubleto\App\Community\Calendar\Models\RecordManagers\Activity
{
  public $table = 'order_activities';

  /** @return BelongsTo<Order, covariant OrderActivity> */
  public function ORDER(): BelongsTo
  {
    return $this->belongsTo(Order::class, 'id_order', 'id');
  }

}
