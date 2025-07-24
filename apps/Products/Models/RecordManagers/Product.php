<?php

namespace HubletoApp\Community\Products\Models\RecordManagers;

use HubletoApp\Community\Suppliers\Models\RecordManagers\Supplier;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends \Hubleto\Framework\RecordManager
{
  public $table = 'products';

  /** @return HasOne<Group, covariant Product> */
  public function GROUP(): HasOne
  {
    return $this->hasOne(Group::class, 'id', 'id_product_group');
  }

  /** @return HasOne<Supplier, covariant Product> */
  public function SUPPLIER(): HasOne
  {
    return $this->hasOne(Supplier::class, 'id', 'id_supplier');
  }

  public function prepareLookupQuery(string $search): mixed
  {
    $query = parent::prepareLookupQuery($search);

    $main = \HubletoMain\Loader::getGlobalApp();
    if ($main->urlParamAsBool("getServices") == true) {
      $query->where("type", \HubletoApp\Community\Products\Models\Product::TYPE_SERVICE);
    } elseif ($main->urlParamAsBool("getProducts") == true) {
      $query->where("type", \HubletoApp\Community\Products\Models\Product::TYPE_PRODUCT);
    }
    return $query;
  }

  public function prepareLookupData(array $dataRaw): array
  {
    $data = parent::prepareLookupData($dataRaw);

    foreach ($dataRaw as $key => $value) {
      $data[$key]['unit_price'] = $value['unit_price'];
      $data[$key]['vat'] = $value['vat'];
    }

    return $data;
  }

}
