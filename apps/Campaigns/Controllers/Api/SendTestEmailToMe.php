<?php

namespace Hubleto\App\Community\Campaigns\Controllers\Api;

use Hubleto\App\Community\Campaigns\Models\Campaign;
use Hubleto\App\Community\Campaigns\Models\CampaignContact;
use Hubleto\App\Community\Mail\Models\Mail;
use Hubleto\App\Community\Campaigns\Lib;

class SendTestEmailToMe extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): ?array
  {
    $idCampaign = $this->router()->urlParamAsInteger('idCampaign');

    $result = [];

    try {

      /** @var Campaign */
      $mCampaign = $this->getModel(Campaign::class);

      /** @var Mail */
      $mMail = $this->getModel(Mail::class);

      $campaign = $mCampaign->record->prepareReadQuery()
        ->where('campaigns.id', $idCampaign)
        ->first()
      ;

      $bodyHtml = Lib::getMailPreview($campaign->toArray(), []);

      $mMail->createAndSend([
        'subject' => $campaign->MAIL_TEMPLATE->subject,
        'body_html' => $bodyHtml,
        'id_account' => $campaign->id_mail_account,
        'from' => $campaign->MAIL_ACCOUNT->sender_email ?? '',
        'to' => $this->authProvider()->getUserEmail(),
        'datetime_created' => date('Y-m-d H:i:s'),
      ]);

      return ['status' => 'success'];
    } catch (\Throwable $e) {
      return ['status' => 'error', 'message' => $e->getMessage()];
    }

    return $result;
  }
}
