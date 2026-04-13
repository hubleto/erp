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
    $email = $this->router()->urlParamAsString('email');
    $emails = $this->router()->urlParamAsArray('emails');

    /** @var Recipient */
    $mRecipient = $this->getModel(Recipient::class);

    try {
      $recipientsDeleted = 0;

      if (!empty($email)) {
        $recipientsDeleted += $mRecipient->record
          ->where('id_campaign', $idCampaign)
          ->where('email', $email)
          ->delete()
        ;
      }

      foreach ($emails as $tmpEmail) {
        if (!empty($tmpEmail)) {
          $recipientsDeleted += $mRecipient->record
            ->where('id_campaign', $idCampaign)
            ->where('email', $tmpEmail)
            ->delete()
          ;
        }
      }

      return [
        "status" => "success",
        "recipientsDeleted" => $recipientsDeleted,
      ];
    } catch (\Throwable $e) {
      return [
        "status" => "failed",
        "error" => $e->getMessage(),
      ];
    }
  }
}
