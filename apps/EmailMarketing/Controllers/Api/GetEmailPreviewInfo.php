<?php

namespace Hubleto\App\Community\EmailMarketing\Controllers\Api;

use Hubleto\App\Community\EmailMarketing\Models\Recipient;
use Hubleto\App\Community\EmailMarketing\Lib;
use Hubleto\App\Community\EmailMarketing\Models\CampaignScheduleRecipient;

class GetEmailPreviewInfo extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {
    $idRecipient = $this->router()->urlParamAsInteger('idRecipient');

    /** @var Recipient */
    $mRecipient = $this->getModel(Recipient::class);

    /** @var CampaignScheduleRecipient */
    $mCampaignScheduleRecipient = $this->getModel(CampaignScheduleRecipient::class);

    $bodyHtml = '';
    $campaignSchedule = null;

    $recipient = $mRecipient->record
      ->where('id', $idRecipient)
      ->with('CONTACT.VALUES')
      ->with('CAMPAIGN')
      ->with('CAMPAIGN.SCHEDULES')
      ->with('CAMPAIGN.SCHEDULES.EMAIL')
      ->with('MAIL')
      ->first();

    if ($recipient) {
      $bodyHtml = Lib::getMailPreview(
        $recipient->MAIL?->toArray() ?? [],
        $recipient->toArray(),
      );

      $campaignSchedule = $mCampaignScheduleRecipient->record
        ->where('id_recipient', $idRecipient)
        ->with('CAMPAIGN_SCHEDULE')
        ->with('CAMPAIGN_SCHEDULE.CAMPAIGN')
        ->with('MAIL')
        ->get()
      ;
    }

    return [
      'CAMPAIGN' => $recipient->CAMPAIGN->toArray(),
      'scheduledMails' => $campaignSchedule?->toArray(),
      'bodyHtml' => $bodyHtml,
    ];
  }
}
