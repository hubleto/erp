<?php

namespace HubletoApp\Community\Products\Models\Eloquent;

use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends \HubletoMain\Core\ModelEloquent
{
  public $table = 'products';

  /** @return HasOne<Group, covariant Product> */
  public function GROUP(): HasOne
  {
    return $this->hasOne(Group::class, 'id','id_product_group');
  }

  /** @return HasOne<Supplier, covariant Product> */
  public function SUPPLIER(): HasOne
  {
    return $this->hasOne(Supplier::class, 'id','id_supplier');
  }
}