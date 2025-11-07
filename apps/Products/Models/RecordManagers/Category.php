<?php

namespace Hubleto\App\Community\Products\Models\RecordManagers;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Category extends \Hubleto\Erp\RecordManager
{
  public $table = 'product_categories';

  /** @return HasOne<Group, covariant Product> */
  public function PARENT(): BelongsTo
  {
    return $this->belongsTo(Category::class, 'id', 'id_parent');
  }

}
