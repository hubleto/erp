<?php

namespace Hubleto\App\Community\Campaigns\Controllers\Api;

use Hubleto\App\Community\Contacts\Models\Contact;
use Hubleto\App\Community\Campaigns\Models\CampaignContact;
use Hubleto\App\Community\Campaigns\Lib;

class GetMailPreviewInfo extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): ?array
  {
    $idCampaign = $this->router()->urlParamAsInteger('idCampaign');
    $idContact = $this->router()->urlParamAsInteger('idContact');

    $mContact = $this->getService(Contact::class);
    $mCampaignContact = $this->getService(CampaignContact::class);

    $bodyHtml = '';

    $campaignContact = $mCampaignContact->record
      ->where('id_campaign', $idCampaign)
      ->where('id_contact', $idContact)
      ->with('CAMPAIGN.MAIL_TEMPLATE')
      ->with('CONTACT.VALUES')
      ->with('MAIL')
      ->first();

    if ($campaignContact) {
      $bodyHtml = Lib::getMailPreview(
        $campaignContact->CAMPAIGN->toArray(),
        $campaignContact->CONTACT->toArray(),
      );
    }

    return [
      'bodyHtml' => $bodyHtml,
      'CONTACT' => $campaignContact ? $campaignContact->CONTACT->toArray() : null,
      'MAIL' => $campaignContact ? $campaignContact->MAIL->toArray() : null,
    ];
  }
}
