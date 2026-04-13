<?php

namespace Hubleto\App\Community\Campaigns\Controllers\Api;

use Hubleto\App\Community\Campaigns\Models\Recipient;
use Hubleto\App\Community\Campaigns\Models\Campaign;
use Hubleto\App\Community\Campaigns\Lib;

class RemoveRecipientFromCampaign extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {
    $idCampaign = $this->router()->urlParamAsInteger('idCampaign');
    $email = $this->router()->urlParamAsInteger('email');

    /** @var Recipient */
    $mRecipient = $this->getModel(Recipient::class);

    try {
      $mRecipient->record
        ->where('id_campaign', $idCampaign)
        ->where('email', $email)
        ->delete()
      ;

      return ["status" => "success"];
    } catch (\Throwable $e) {
      return [
        "status" => "failed",
        "error" => $e->getMessage(),
      ];
    }
  }
}
