<?php

namespace Hubleto\App\Community\EmailMarketing\Controllers\Api;


use Hubleto\App\Community\EmailMarketing\Models\Email;
use Hubleto\App\Community\EmailMarketing\Models\Recipient;
use Hubleto\App\Community\EmailMarketing\Lib;

class ImportEmails extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {
    $emails = $this->router()->urlParamAsString('emails');
    $idCampaign = $this->router()->urlParamAsInteger('idCampaign');
    $idEmail = $this->router()->urlParamAsInteger('idEmail');

    /** @var Recipient */
    $mRecipient = $this->getModel(Recipient::class);

    $recipientsImported = 0;

    try {

      foreach (explode("\n", $emails) as $email) {
        $email = strtolower(trim($email));

        if (empty($email)) continue;
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) continue;

        $query = $mRecipient->record->where('email', 'like', $email);
        if ($idCampaign > 0) $query = $query->where('id_campaign', $idCampaign);
        if ($idEmail > 0) $query = $query->where('id_email', $idEmail);

        $recipientExists = $query->count() > 0;

        if (!$recipientExists) {
          $mRecipient->record->create([
            'id_campaign' => $idCampaign,
            'id_email' => $idEmail,
            'email' => $email,
            'date_added' => date('Y-m-d'),
          ]);
          $recipientsImported++;
        }
      }

      Lib::scheduleMissingEmailsInCampaign($idCampaign);

      return ['status' => 'success', 'recipientsImported' => $recipientsImported];

    } catch (\Throwable $e) {
      return ['status' => 'error', 'message' => $e->getMessage()];
    }

    return $result;
  }
}
