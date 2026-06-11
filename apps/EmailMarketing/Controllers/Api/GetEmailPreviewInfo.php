<?php

namespace Hubleto\App\Community\EmailMarketing\Controllers\Api;

use Hubleto\App\Community\EmailMarketing\Models\EmailRecipient;
use Hubleto\App\Community\EmailMarketing\Lib;

class GetEmailPreviewInfo extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {
    $idRecipient = $this->router()->urlParamAsInteger('idRecipient');

    $mRecipient = $this->getModel(EmailRecipient::class);

    $bodyHtml = '';

    $recipient = $mRecipient->record
      ->where('id', $idRecipient)
      ->with('CONTACT.VALUES')
      ->with('MAIL')
      ->first();

    if ($recipient) {
      $bodyHtml = Lib::getMailPreview(
        $recipient->EMAIL->toArray(),
        $recipient->toArray(),
      );
    }

    return [
      'bodyHtml' => $bodyHtml,
    ];
  }
}
