<?php

namespace Hubleto\App\Community\EmailMarketing\Controllers\Api;

use Hubleto\App\Community\EmailMarketing\Models\Recipient;

class RemoveRecipientFromEmail extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {
    $idEmail = $this->router()->urlParamAsInteger('idEmail');
    $email = $this->router()->urlParamAsString('email');
    $emails = $this->router()->urlParamAsArray('emails');

    /** @var Recipient */
    $mRecipient = $this->getModel(Recipient::class);

    try {
      $recipientsDeleted = 0;

      if (!empty($email)) {
        $recipientsDeleted += $mRecipient->record
          ->where('id_email', $idEmail)
          ->where('email', $email)
          ->delete()
        ;
      }

      foreach ($emails as $tmpEmail) {
        if (!empty($tmpEmail)) {
          $recipientsDeleted += $mRecipient->record
            ->where('id_email', $idEmail)
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
