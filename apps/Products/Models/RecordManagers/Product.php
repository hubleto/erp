<?php

namespace Hubleto\App\Community\Products\Models\RecordManagers;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends \Hubleto\Erp\RecordManager
{
  public $table = 'products';

  /** @return HasOne<Group, covariant Product> */
  public function GROUP(): BelongsTo
  {
    return $this->belongsTo(Group::class, 'id_group', 'id');
  }

  /** @return HasOne<Group, covariant Product> */
  public function CATEGORY(): BelongsTo
  {
    return $this->belongsTo(Category::class, 'id_category', 'id');
  }

  public function prepareReadQuery(mixed $query = null, int $level = 0, array|null $includeRelations = null): mixed
  {
    $query = parent::prepareReadQuery($query, $level, $includeRelations);

    $hubleto = \Hubleto\Erp\Loader::getGlobalApp();
    $idCategory = $hubleto->router()->urlParamAsInteger('idCategory');

    if ($idCategory > 0) {
      $query = $query->where($this->table . '.id_category', $idCategory);
    }

    return $query;
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
