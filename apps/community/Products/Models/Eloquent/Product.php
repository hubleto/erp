<?php

namespace HubletoApp\Community\Products\Models\Eloquent;

use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends \HubletoMain\Core\ModelEloquent
{
  public $table = 'products';

  public function GROUP(): HasOne {
    return $this->hasOne(Group::class, 'id','id_product_group');
  }
  public function SUPPLIER(): HasOne {
    return $this->hasOne(Supplier::class, 'id','id_supplier');
  }
}