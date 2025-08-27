<?php

namespace HubletoApp\Community\Projects\Models\RecordManagers;

use HubletoApp\Community\Orders\Models\RecordManagers\Order;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectOrder extends \HubletoMain\RecordManager
{
  public $table = 'projects_orders';

  /** @return BelongsTo<Product, covariant OrderProduct> */
  public function PROJECT(): BelongsTo
  {
    return $this->belongsTo(Project::class, 'id_project', 'id');
  }

  /** @return BelongsTo<Order, covariant OrderProduct> */
  public function ORDER(): BelongsTo
  {
    return $this->belongsTo(Order::class, 'id_order', 'id');
  }

}
