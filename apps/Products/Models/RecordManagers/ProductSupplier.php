<?php

namespace Hubleto\App\Community\Products\Models\RecordManagers;

use Hubleto\App\Community\Suppliers\Models\RecordManagers\Supplier;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ProductSupplier extends \Hubleto\Erp\RecordManager
{
  public $table = 'products_suppliers';

  /** @return HasOne<Group, covariant Product> */
  public function PRODUCT(): HasOne
  {
    return $this->hasOne(Product::class, 'id', 'id_product');
  }

  /** @return HasOne<Supplier, covariant Product> */
  public function SUPPLIER(): HasOne
  {
    return $this->hasOne(Supplier::class, 'id', 'id_supplier');
  }

}
