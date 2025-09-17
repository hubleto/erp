<?php

namespace Hubleto\App\Community\Campaigns\Controllers\Api;

use Hubleto\App\Community\Contacts\Models\Contact;
use Hubleto\App\Community\Campaigns\Models\Recipient;
use Hubleto\App\Community\Campaigns\Lib;

class GetMailPreviewInfo extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): ?array
  {
    $idCampaign = $this->router()->urlParamAsInteger('idCampaign');
    $idContact = $this->router()->urlParamAsInteger('idContact');

    $mContact = $this->getService(Contact::class);
    $mRecipient = $this->getService(Recipient::class);

    $bodyHtml = '';

    $recipient = $mRecipient->record
      ->where('id_campaign', $idCampaign)
      ->where('id_contact', $idContact)
      ->with('CAMPAIGN.MAIL_TEMPLATE')
      ->with('CONTACT.VALUES')
      ->with('MAIL')
      ->first();

    if ($recipient) {
      $bodyHtml = Lib::getMailPreview(
        $recipient->CAMPAIGN->toArray(),
        $recipient->CONTACT->toArray(),
      );
    }

    return [
      'bodyHtml' => $bodyHtml,
      'CONTACT' => $recipient?->CONTACT ? $recipient?->CONTACT->toArray() : null,
      'MAIL' => $recipient?->MAIL ? $recipient?->MAIL->toArray() : null,
    ];
  }
}
