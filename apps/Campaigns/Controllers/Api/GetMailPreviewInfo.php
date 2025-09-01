<?php

namespace Hubleto\App\Community\Campaigns\Controllers\Api;

use Hubleto\App\Community\Contacts\Models\Contact;
use Hubleto\App\Community\Campaigns\Models\CampaignContact;
use Hubleto\App\Community\Campaigns\Lib;

class GetMailPreviewInfo extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): ?array
  {
    $idCampaign = $this->getRouter()->urlParamAsInteger('idCampaign');
    $idContact = $this->getRouter()->urlParamAsInteger('idContact');

    $mContact = $this->getService(Contact::class);
    $mCampaignContact = $this->getService(CampaignContact::class);

    $bodyHtml = '';

    $campaignContact = $mCampaignContact->record
      ->where('id_campaign', $idCampaign)
      ->where('id_contact', $idContact)
      ->with('CAMPAIGN')
      ->with('CONTACT.VALUES')
      ->first();

    if ($campaignContact) {
      $bodyHtml = Lib::routeLinksThroughCampaignTracker(
        $campaignContact->CAMPAIGN->toArray(),
        $campaignContact->CONTACT->toArray(),
        $campaignContact->CAMPAIGN->mail_body
      );
    }

    return [
      'bodyHtml' => $bodyHtml,
      'CONTACT' => $campaignContact ? $campaignContact->CONTACT->toArray() : null,
    ];
  }
}
