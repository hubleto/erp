<?php

namespace Hubleto\App\Community\Campaigns\Controllers\Api;

use Hubleto\App\Community\Contacts\Models\Contact;
use Hubleto\App\Community\Campaigns\Models\Recipient;
use Hubleto\App\Community\Campaigns\Lib;

class GetMailPreviewInfo extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): ?array
  {
    $idRecipient = $this->router()->urlParamAsInteger('idRecipient');

    $mRecipient = $this->getService(Recipient::class);

    $bodyHtml = '';

    $recipient = $mRecipient->record
      ->where('id', $idRecipient)
      ->with('CAMPAIGN.MAIL_TEMPLATE')
      ->with('CONTACT.VALUES')
      ->with('MAIL')
      ->first();

    if ($recipient) {
      $bodyHtml = Lib::getMailPreview(
        $recipient->CAMPAIGN->toArray(),
        $recipient->toArray(),
      );
    }

    return [
      'bodyHtml' => $bodyHtml,
    ];
  }
}
