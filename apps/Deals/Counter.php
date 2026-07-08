<?php

namespace Hubleto\App\Community\Deals;

use Hubleto\Erp\Core;

class Counter extends Core
{

  /**
   * [Description for queryForOpenDealsWithoutFuturePlan]
   *
   * @return mixed
   * 
   */
  public function queryForOpenDealsWithoutFuturePlan(): mixed
  {
    $mDeal = $this->getModel(Models\Deal::class);

    return $mDeal->record->prepareReadQuery()
      ->whereDoesntHave('ACTIVITIES', function($q) {
        $q->where('completed', false);
        $q->whereDate('date_start', '>=', date("Y-m-d"));
      })
      ->where($mDeal->table . '.is_closed', false)
    ;

  }

  /**
   * [Description for openDealsWithoutFuturePlan]
   *
   * @return int
   * 
   */
  public function openDealsWithoutFuturePlan(): int
  {
    return $this->queryForOpenDealsWithoutFuturePlan()->count();
  }

}
