<?php

namespace HubletoApp\Community\Deals\Controllers\Api;

use HubletoApp\Community\Deals\Models\Deal;
use HubletoApp\Community\Deals\Models\DealActivity;

class LogActivity extends \HubletoMain\Core\Controllers\Controller
{
  public int $returnType = \ADIOS\Core\Controller::RETURN_TYPE_JSON;

  public function renderJson(): ?array
  {
    $idDeal = $this->main->urlParamAsInteger("idDeal");
    $activity = $this->main->urlParamAsString("activity");
    if ($idDeal > 0 && $activity != '') {
      $mDeal = new Deal($this->main);
      $deal = $mDeal->record->find($idDeal)->first()?->toArray();

      if ($deal && $deal['id'] > 0) {
        $mDealActivity = new DealActivity($this->main);
        $mDealActivity->record->recordCreate([
          'id_deal' => $idDeal,
          'subject' => $activity,
          'date_start' => date('Y-m-d'),
          'time_start' => date('H:i:s'),
          'all_day' => true,
          'completed' => true,
          'id_user' => $this->main->auth->getUserId(),
        ]);
      }
    }

    return [
      "status" => "success",
      "idDeal" => $idDeal,
    ];
  }

}
