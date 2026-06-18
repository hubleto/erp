<?php

namespace Hubleto\App\Community\Deals;

use Hubleto\Erp\Core;

class Counter extends Core
{

  /**
   * [Description for openDealsWithoutFuturePlan]
   *
   * @return int
   * 
   */
  public function openDealsWithoutFuturePlan(): int
  {
    $mDeal = $this->getModel(Models\Deal::class);
    $idUser = $this->authProvider()->getUserId();

    return $mDeal->record->prepareReadQuery()
      ->whereDoesntHave('ACTIVITIES', function($q) {
        $q->where('completed', false);
        $q->whereDate('date_start', '>=', date("Y-m-d"));
      })
      ->where($mDeal->table . '.is_closed', false)
      ->where(function($q) use ($mDeal, $idUser) {
        $q->where($mDeal->table . '.id_owner', $idUser);
        $q->orWhere($mDeal->table . '.id_manager', $idUser);
      })
      ->count()
    ;

  }

}
