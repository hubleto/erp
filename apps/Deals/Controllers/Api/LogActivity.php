<?php

namespace HubletoApp\Community\Deals\Controllers\Api;

use HubletoApp\Community\Deals\Models\Deal;
use HubletoApp\Community\Deals\Models\DealActivity;

class LogActivity extends \HubletoMain\Controllers\ApiController
{
  public function renderJson(): ?array
  {
    $idDeal = $this->getRouter()->urlParamAsInteger("idDeal");
    $activity = $this->getRouter()->urlParamAsString("activity");
    if ($idDeal > 0 && $activity != '') {
      $mDeal = $this->getService(Deal::class);
      $deal = $mDeal->record->find($idDeal)->first()?->toArray();

      if ($deal && $deal['id'] > 0) {
        $mDealActivity = $this->getService(DealActivity::class);
        $mDealActivity->record->recordCreate([
          'id_deal' => $idDeal,
          'subject' => $activity,
          'date_start' => date('Y-m-d'),
          'time_start' => date('H:i:s'),
          'all_day' => true,
          'completed' => true,
          'id_owner' => $this->getAuthProvider()->getUserId(),
        ]);
      }
    }

    return [
      "status" => "success",
      "idDeal" => $idDeal,
    ];
  }

}
