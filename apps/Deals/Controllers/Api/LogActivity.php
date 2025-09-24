<?php

namespace Hubleto\App\Community\Deals\Controllers\Api;


use Hubleto\App\Community\Deals\Models\Deal;
use Hubleto\App\Community\Deals\Models\DealActivity;

class LogActivity extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {
    $idDeal = $this->router()->urlParamAsInteger("idDeal");
    $activity = $this->router()->urlParamAsString("activity");
    if ($idDeal > 0 && $activity != '') {
      $mDeal = $this->getModel(Deal::class);
      /** @var Deal */
      $deal = $mDeal->record->find($idDeal)->first()?->toArray();

      if ($deal && $deal['id'] > 0) {
        /** @var DealActivity */
        $mDealActivity = $this->getModel(DealActivity::class);
        $mDealActivity->record->recordCreate([
          'id_deal' => $idDeal,
          'subject' => $activity,
          'date_start' => date('Y-m-d'),
          'time_start' => date('H:i:s'),
          'all_day' => true,
          'completed' => true,
          'id_owner' => $this->getService(\Hubleto\Framework\AuthProvider::class)->getUserId(),
        ]);
      }
    }

    return [
      "status" => "success",
      "idDeal" => $idDeal,
    ];
  }

}
