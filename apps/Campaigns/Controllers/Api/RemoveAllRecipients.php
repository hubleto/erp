<?php

namespace Hubleto\App\Community\Campaigns\Controllers\Api;


use Hubleto\App\Community\Campaigns\Models\Campaign;
use Hubleto\App\Community\Campaigns\Models\Recipient;
use Hubleto\App\Community\Mail\Models\Mail;
use Hubleto\App\Community\Campaigns\Lib;

class RemoveAllRecipients extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {
    $idCampaign = $this->router()->urlParamAsInteger('idCampaign');

    /** @var Recipient */
    $mRecipient = $this->getModel(Recipient::class);

    try {

      $deleted = $mRecipient->record->where('id_campaign', $idCampaign)->delete();

      return ['status' => 'success', 'deleted' => $deleted];

    } catch (\Throwable $e) {
      return ['status' => 'error', 'message' => $e->getMessage()];
    }
  }
}
