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
    return $mLead->record->prepareReadQuery()
      ->whereDoesntHave('ACTIVITIES', function($q) {
        $q->where('completed', false);
        $q->whereDate('date_start', '>=', date("Y-m-d"));
      })
      ->where($mLead->table . '.is_closed', false)
      ->count()
    ;

  }

}
