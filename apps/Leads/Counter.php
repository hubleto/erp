<?php

namespace Hubleto\App\Community\Leads;

use Hubleto\Erp\Core;

class Counter extends Core
{

  /**
   * [Description for openLeadsWithoutFuturePlan]
   *
   * @return int
   * 
   */
  public function openLeadsWithoutFuturePlan(): int
  {
    $mLead = $this->getModel(Models\Lead::class);
    $idUser = $this->authProvider()->getUserId();

    return $mLead->record->prepareReadQuery()
      ->whereDoesntHave('ACTIVITIES', function($q) {
        $q->where('completed', false);
        $q->whereDate('date_start', '>=', date("Y-m-d"));
      })
      ->where($mLead->table . '.is_closed', false)
      ->where(function($q) use ($mLead, $idUser) {
        $q->where($mLead->table . '.id_owner', $idUser);
        $q->orWhere($mLead->table . '.id_manager', $idUser);
      })
      ->count()
    ;

  }

}
