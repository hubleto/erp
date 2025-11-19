<?php

namespace Hubleto\App\Community\Deals\Controllers\Api;

use Exception;
use Hubleto\App\Community\Deals\Models\Deal;
use Hubleto\App\Community\Deals\Models\DealLead;
use Hubleto\App\Community\Leads\Models\Lead;

class SetParentLead extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {
    $idDeal = $this->router()->urlParamAsInteger("idDeal");
    $idLead = $this->router()->urlParamAsInteger("idLead");

    if ($idDeal <= 0 || $idLead <= 0) {
      return [
        "status" => "failed",
        "error" => "The deal or lead not set."
      ];
    }

    /** @var DealLead */
    $mDealLead = $this->getModel(DealLead::class);

    try {
      $mDealLead->record->where('id_deal', $idDeal)->delete();
      $mDealLead->record->create([
        'id_deal' => $idDeal,
        'id_lead' => $idLead,
      ]);
    } catch (Exception $e) {
      return [
        "status" => "failed",
        "error" => $e
      ];
    }

    return [
      "status" => "success",
      "idDeal" => $idDeal,
    ];
  }

}
