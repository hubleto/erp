<?php

namespace Hubleto\App\Community\Campaigns\Controllers\Api;


use Hubleto\App\Community\Campaigns\Models\Campaign;
use Hubleto\App\Community\Campaigns\Models\Recipient;
use Hubleto\App\Community\Mail\Models\Mail;
use Hubleto\App\Community\Campaigns\Lib;

class ImportEmails extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {
    $emails = $this->router()->urlParamAsString('emails');
    $idCampaign = $this->router()->urlParamAsInteger('idCampaign');

    /** @var Campaign */
    $mCampaign = $this->getModel(Campaign::class);

    /** @var Recipient */
    $mRecipient = $this->getModel(Recipient::class);

    $recipientsImported = 0;

    try {

      foreach (explode("\n", $emails) as $email) {
        $email = strtolower(trim($email));

        if (empty($email)) continue;
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) continue;

        $recipientExists = $mRecipient->record
          ->where('id_campaign', $idCampaign)
          ->where('email', $email)
          ->count()
          > 0
        ;

        if (!$recipientExists) {
          $mRecipient->record->create([
            'id_campaign' => $idCampaign,
            'email' => $email,
          ]);
          $recipientsImported++;
        }
      }

      return ['status' => 'success', 'recipientsImported' => $recipientsImported];

    } catch (\Throwable $e) {
      return ['status' => 'error', 'message' => $e->getMessage()];
    }

    return $result;
  }
}
