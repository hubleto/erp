<?php

namespace Hubleto\App\Community\Products\Models\RecordManagers;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends \Hubleto\Erp\RecordManager
{
  public $table = 'products';

  /** @return BelongsTo */
  public function GROUP(): BelongsTo
  {
    return $this->belongsTo(Group::class, 'id_group', 'id');
  }

  /** @return BelongsTo */
  public function CATEGORY(): BelongsTo
  {
    return $this->belongsTo(Category::class, 'id_category', 'id');
  }

  /** @return HasMany */
  public function PACKAGING(): HasMany
  {
    return $this->hasMany(ProductPackaging::class, 'id_product', 'id')->orderBy('sort')->with('UNIT');
  }

  public function prepareReadQuery(mixed $query = null, int $level = 0, array|null $includeRelations = null): mixed
  {
    $query = parent::prepareReadQuery($query, $level, $includeRelations);

    $hubleto = \Hubleto\Erp\Loader::getGlobalApp();
    $idCategory = $hubleto->router()->urlParamAsInteger('idCategory');

    if ($idCategory > 0) {
      $query = $query->whereIn($this->table . '.id_category', $this->categorySubtreeIds($idCategory));
    }

    return $query;
  }

  protected function categorySubtreeIds(int $idRootCategory): array
  {
    $categoryRows = \Hubleto\Erp\Loader::getGlobalApp()
      ->getModel(\Hubleto\App\Community\Products\Models\Category::class)
      ->record->get(['id', 'id_parent'])->toArray();

    $childrenByParent = [];
    foreach ($categoryRows as $categoryRow) {
      $childrenByParent[(int) ($categoryRow['id_parent'] ?? 0)][] = (int) $categoryRow['id'];
    }

    $subtreeIds = [];
    $queue = [$idRootCategory];
    while (!empty($queue)) {
      $currentId = array_shift($queue);
      if (isset($subtreeIds[$currentId])) continue;
      $subtreeIds[$currentId] = true;
      foreach ($childrenByParent[$currentId] ?? [] as $childId) $queue[] = $childId;
    }

    return array_keys($subtreeIds);
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
