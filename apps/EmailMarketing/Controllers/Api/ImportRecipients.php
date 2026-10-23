<?php

namespace Hubleto\App\Community\EmailMarketing\Controllers\Api;


use Hubleto\App\Community\EmailMarketing\Models\Email;
use Hubleto\App\Community\EmailMarketing\Models\Recipient;
use Hubleto\App\Community\EmailMarketing\Lib;
use Hubleto\App\Community\EmailMarketing\Models\Campaign;

class ImportRecipients extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {
    $recipients = $this->router()->urlParamAsString('recipients');
    $idCampaign = $this->router()->urlParamAsInteger('idCampaign');
    $idEmail = $this->router()->urlParamAsInteger('idEmail');

    /** @var Campaign */
    $mCampaign = $this->getModel(Campaign::class);
    $campaign = $mCampaign->record->where('id', $idCampaign)->first();
    if (!$campaign) throw new \Exception("Campaign not found");

    if ($idEmail > 0) {
      /** @var Email */
      $mEmail = $this->getModel(Email::class);
      $email = $mEmail->record->where('id', $idEmail)->first();
      if (!$email) throw new \Exception("Email not found");
    }

    /** @var Recipient */
    $mRecipient = $this->getModel(Recipient::class);

    $recipientsImported = 0;

    try {

      foreach (explode("\n", $recipients) as $recipient) {
        $recipientParsed = @json_decode($recipient, true);

        if (is_array($recipientParsed)) {
          $email = strtolower(trim($recipientParsed[0]));
          $variables = $recipientParsed[1] ?? [];
        } else {
          $email = strtolower(trim($recipient));
          $variables = [];
        }

        if (empty($email)) continue;
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) continue;

        $query = $mRecipient->record->where('email', 'like', $email);
        if ($idCampaign > 0) $query = $query->where('id_campaign', $idCampaign);
        if ($idEmail > 0) $query = $query->where('id_email', $idEmail);

        $recipientExists = $query->count() > 0;

        if (!$recipientExists) {
          $recipientData = [
            'email' => $email,
            'variables' => json_encode($variables),
            'date_added' => date('Y-m-d'),
          ];

          if ($idCampaign > 0) $recipientData['id_campaign'] = $idCampaign;
          if ($idEmail > 0) $recipientData['id_email'] = $idEmail;

          $mRecipient->record->create($recipientData);
          $recipientsImported++;
        }
      }

      if ($campaign->type == Campaign::TYPE_NURTURING) {
        Lib::scheduleMissingEmailsInCampaign($idCampaign);
      }

      return ['status' => 'success', 'recipientsImported' => $recipientsImported];

    } catch (\Throwable $e) {
      return ['status' => 'error', 'message' => $e->getMessage()];
    }

    return $result;
  }
}
