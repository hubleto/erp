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

    $recipients = $mRecipient->record
      ->where('id_campaign', $idCampaign)
      ->with('MAIL')
      ->with('STATUS')
      ->with('CLICKS')
      ->get()
      ?->toArray()
    ;

    foreach ($recipients as $key => $recipient) {
      unset($recipients[$key]['MAIL']['body_text']);
      unset($recipients[$key]['MAIL']['body_html']);

      $botScoreGroups = [];
      if (is_array($recipient['CLICKS'])) {
        foreach ($recipient['CLICKS'] as $click) {
          $ts = round(strtotime($click->datetime_clicked) / 5000); // 5-second interval to group the clicks
          if (!isset($botScoreGroups[$ts*5000])) $botScoreGroups[$ts*5000] = 0;
          $botScoreGroups[$ts*5000] += $click->bot_score;
        }
      }

      $recipients[$key]['BOT_SCORE_GROUPED_BY_TIMESTAMP'] = $botScoreGroups;
    }

    $launchInfo = [
      'recipients' => $recipients,
      'recentlyContacted' => []
    ];

    $contactIds = $mRecipient->record->where('id_campaign', $idCampaign)->pluck('id_contact');

    $recentlyContacted = $mRecipient->record
      ->where('id_campaign', '!=', $idCampaign)
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
