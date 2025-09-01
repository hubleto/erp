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
      ->with('CAMPAIGN.MAIL_TEMPLATE')
      ->with('CONTACT.VALUES')
      ->first();

    if ($campaignContact) {
      $campaign = $campaignContact->CAMPAIGN->toArray();
      $contact = $campaignContact->CONTACT->toArray();
      $template = $campaign['MAIL_TEMPLATE'];

      $bodyHtml = Lib::addUtmVariablesToEmailLinks(
        (string) $template['body_html'],
        (string) $campaign['utm_source'],
        (string) $campaign['utm_campaign'],
        (string) $campaign['utm_term'],
        (string) $campaign['utm_content'],
      );

      $bodyHtml = Lib::routeLinksThroughCampaignTracker(
        $campaign,
        $contact,
        $bodyHtml,
      );
    }

    return [
      'bodyHtml' => $bodyHtml,
      'CONTACT' => $campaignContact ? $campaignContact->CONTACT->toArray() : null,
    ];
  }
}
