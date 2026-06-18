<?php

namespace Hubleto\App\Community\Products\Models\RecordManagers;

class Unit extends \Hubleto\Erp\RecordManager
{
  public $table = 'product_units';

  public function prepareLookupQuery(string $search): mixed
  {
    $query = parent::prepareLookupQuery($search);

    $hubleto = \Hubleto\Erp\Loader::getGlobalApp();
    $unitCategory = $hubleto->router()->urlParamAsInteger('unitCategory');
    if ($unitCategory > 0) {
      $query->where('category', $unitCategory);
    }

    return $query;
  }
}
