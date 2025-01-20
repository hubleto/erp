<?php

namespace HubletoApp\Community\Shop\Models\Eloquent;

use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends \HubletoMain\Core\ModelEloquent
{
  public $table = 'products';
  
  public function GROUP(): HasOne {
    return $this->hasOne(ProductGroup::class, 'id','id_product_group');
  }
  public function SUPPLIER(): HasOne {
    return $this->hasOne(ProductSupplier::class, 'id','id_supplier');
  }
}