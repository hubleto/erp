<?php

namespace Hubleto\App\Community\Products\Models\RecordManagers;

use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends \Hubleto\Erp\RecordManager
{
  public $table = 'products';

  /** @return HasOne<Group, covariant Product> */
  public function GROUP(): HasOne
  {
    return $this->hasOne(Group::class, 'id', 'id_product_group');
  }

  public function prepareLookupQuery(string $search): mixed
  {
    $query = parent::prepareLookupQuery($search);

    $hubleto = \Hubleto\Erp\Loader::getGlobalApp();
    if ($hubleto->router()->urlParamAsBool("getServices") == true) {
      $query->where("type", \Hubleto\App\Community\Products\Models\Product::TYPE_SERVICE);
    } elseif ($hubleto->router()->urlParamAsBool("getProducts") == true) {
      $query->where("type", \Hubleto\App\Community\Products\Models\Product::TYPE_CONSUMABLE);
    }
    return $query;
  }

  public function prepareLookupData(array $dataRaw): array
  {
    $data = parent::prepareLookupData($dataRaw);

    foreach ($dataRaw as $key => $value) {
      $data[$key]['sales_price'] = $value['sales_price'];
      $data[$key]['vat'] = $value['vat'] ?? 0;
    }

    return $data;
  }

}
