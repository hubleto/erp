<?php

namespace Hubleto\App\Community\Campaigns\Controllers;

use Hubleto\App\Community\Campaigns\Models\Campaign;
use Hubleto\App\Community\Campaigns\Models\Click;
use Hubleto\App\Community\Campaigns\Models\Recipient;
use Hubleto\App\Community\Campaigns\Lib;

class MailPreview extends \Hubleto\Erp\Controller
{
  public bool $hideDefaultDesktop = true;
  public bool $requiresAuthenticatedUser = false;

  public function render(): string
  {
    $urlDataB64 = $this->router()->urlParamAsString('c');

    $urlDataJson = @base64_decode($urlDataB64);
    $urlData = @json_decode($urlDataJson, true);

    $campaignUid = $urlData['cuid'] ?? '';
    $idRecipient = (int) ($urlData['rcid'] ?? 0);

    if (empty($campaignUid)) return 'Unknown campaign.';
    // if (empty($idRecipient)) return 'Unknown recipient.';

    /** @var Campaign */
    $mCampaign = $this->getModel(Campaign::class);

    /** @var Recipient */
    $mRecipient = $this->getModel(Recipient::class);

    $campaign = $mCampaign->record->where('uid', $campaignUid)->with('MAIL_TEMPLATE')->first();

    $recipient = $mRecipient->record
      ->where('id', $idRecipient)
      ->where('id_campaign', $campaign->id)
      ->with('CAMPAIGN.MAIL_TEMPLATE')
      ->with('CONTACT.VALUES')
      ->with('MAIL')
      ->first();

    $bodyHtml = Lib::getMailPreview(
      $campaign->toArray(),
      ($recipient ? $recipient->toArray() : []),
    );

    return $bodyHtml;

  }

}
