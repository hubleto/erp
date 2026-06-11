<?php

namespace Hubleto\App\Community\EmailMarketing\Controllers\Api;


use Hubleto\App\Community\EmailMarketing\Models\Email;
use Hubleto\App\Community\EmailMarketing\Models\EmailRecipient;

class ImportEmails extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {
    $emails = $this->router()->urlParamAsString('emails');
    $idEmail = $this->router()->urlParamAsInteger('idEmail');

    /** @var Email */
    $mEmail = $this->getModel(Email::class);

    /** @var EmailRecipient */
    $mRecipient = $this->getModel(EmailRecipient::class);

    $recipientsImported = 0;

    try {

      foreach (explode("\n", $emails) as $email) {
        $email = strtolower(trim($email));

        if (empty($email)) continue;
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) continue;

        $recipientExists = $mRecipient->record
          ->where('id_email', $idEmail)
          ->where('email', $email)
          ->count()
          > 0
        ;

        if (!$recipientExists) {
          $mRecipient->record->create([
            'id_email' => $idEmail,
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
