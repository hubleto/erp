<?php

namespace Hubleto\App\Community\Campaigns\Controllers\Api;

use Hubleto\App\Community\Campaigns\Models\Recipient;
use Hubleto\App\Community\Campaigns\Models\Campaign;
use Hubleto\App\Community\Campaigns\Lib;

class GetCampaignLaunchInfo extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {
    $idCampaign = $this->router()->urlParamAsInteger('idCampaign');

    /** @var Campaign */
    $mCampaign = $this->getModel(Campaign::class);

    /** @var Recipient */
    $mRecipient = $this->getModel(Recipient::class);

    $recipients = $mRecipient->record->where('id_campaign', $idCampaign)->with('MAIL')->with('STATUS')->get();

    $launchInfo = [
      'recipients' => $recipients,
      'recentlyContacted' => []
    ];

    $contactIds = $mRecipient->record->where('id_campaign', $idCampaign)->pluck('id_contact');

    $recentlyContacted = $mRecipient->record
      ->whereIn('id_contact', $contactIds)
      ->whereHas('MAIL', function($q) {
        return $q->where('datetime_sent', '>=', date('Y-m-d H:i:s', strtotime('-1 month')));
      })
      ->with('CAMPAIGN')
      ->with('CONTACT.VALUES')
      ->get()
    ;

    foreach ($recentlyContacted as $tmp) {
      $launchInfo['recentlyContacted'][] = [
        'CAMPAIGN' => [
          'id' => $tmp->CAMPAIGN->id,
          'name' => $tmp->CAMPAIGN->name,
        ],
        'CONTACT' => $tmp->CONTACT,
      ];
    }

    return $launchInfo;
  }
}
