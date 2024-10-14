<?php

namespace CeremonyCrmApp\Modules\Sales\Sales\Controllers;

use CeremonyCrmApp\Modules\Core\Settings\Models\DealStatus;
use CeremonyCrmApp\Modules\Sales\Sales\Models\Deal;
use CeremonyCrmApp\Modules\Sales\Sales\Models\DealHistory;
use Exception;

class ChangeDealStatus extends \CeremonyCrmApp\Core\Controller
{

  public function renderJson(): ?array
  {
    $mDeal = new Deal($this->app);
    $mDealHistory = new DealHistory($this->app);
    $mDealStatus = new DealStatus($this->app);

    $status = null;

    try {
      $deal = $mDeal->eloquent->find($this->params["idDeal"]);
      $deal->id_status = $this->params["idStatus"];
      $deal->save();

      $status = $mDealStatus->eloquent->find((int) $this->params["idStatus"]);
      $mDealHistory->eloquent->create([
        "change_date" => date("Y-m-d"),
        "id_deal" => $deal->id,
        "description" => "Status changed to ".$status->name
      ]);
    } catch (Exception $e) {
      return [
        "status" => "failed",
        "error" => $e
      ];
    }

    $dealHistory = $mDealHistory->eloquent->where("id_deal", $deal->id)->get();

    return [
      "status" => "success",
      "returnStatus" => $status->toArray(),
      "dealHistory" => $dealHistory->toArray()
    ];
  }
}
