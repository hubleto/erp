<?php

namespace Hubleto\App\Community\Projects\Models\RecordManagers;

use Hubleto\App\Community\Orders\Models\RecordManagers\Order;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectOrder extends \Hubleto\Erp\RecordManager
{
  public $table = 'projects_orders';

  /** @return BelongsTo<Product, covariant Item> */
  public function PROJECT(): BelongsTo
  {
    return $this->belongsTo(Project::class, 'id_project', 'id');
  }

  /** @return BelongsTo<Order, covariant Item> */
  public function ORDER(): BelongsTo
  {
    return $this->belongsTo(Order::class, 'id_order', 'id');
  }

}
