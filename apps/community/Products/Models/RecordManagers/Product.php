<?php

namespace HubletoApp\Community\Products\Models\RecordManagers;

use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends \HubletoMain\Core\RecordManager
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

  public function prepareReadQuery(mixed $query = null, int $level = 0): mixed
  {
    $query = parent::prepareReadQuery($query, $level);

    $main = \ADIOS\Core\Helper::getGlobalApp();
    if ($main->urlParamAsBool("getServices") == true) $query = $query->where("type", \HubletoApp\Community\Products\Models\Product::TYPE_SERVICE);
    else if ($main->urlParamAsBool("getProducts") == true) $query = $query->where("type", \HubletoApp\Community\Products\Models\Product::TYPE_PRODUCT);

    return $query;
  }

  public function prepareLookupData(array $dataRaw): array
  {
    $data = parent::prepareLookupData($dataRaw);

    foreach ($dataRaw as $key => $value) {
      $data[$key]['_TEST'] = $value['title'];
    }

    return $data;
  }

}