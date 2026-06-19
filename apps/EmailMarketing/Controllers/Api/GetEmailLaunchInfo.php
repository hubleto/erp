<?php

namespace Hubleto\App\Community\EmailMarketing\Controllers\Api;

use Hubleto\App\Community\EmailMarketing\Models\Recipient;
use Hubleto\App\Community\EmailMarketing\Models\Email;
use Hubleto\App\Community\EmailMarketing\Lib;

class GetEmailLaunchInfo extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {
    $idEmail = $this->router()->urlParamAsInteger('idEmail');
    $recentlyContactedPeriod = $this->router()->urlParamAsInteger('recentlyContactedPeriod', 3);

    /** @var Recipient */
    $mRecipient = $this->getModel(Recipient::class);

    $recipients = $mRecipient->record
      ->where('id_email', $idEmail)
      ->with('MAIL')
      ->with('STATUS')
      ->with('CLICKS')
      ->get()
      ?->toArray()
    ;

    foreach ($recipients as $key => $recipient) {
      unset($recipients[$key]['MAIL']['body_text']);
      unset($recipients[$key]['MAIL']['body_html']);

      $grouppingInterval = 2000; // 2-second interval to group the clicks

      if (is_array($recipient['CLICKS'])) {
        foreach ($recipient['CLICKS'] as $click) {
          $ts = round(strtotime((string) ($click['datetime_clicked'] ?? '')) / $grouppingInterval);
          $tsGrouped = $ts * $grouppingInterval;

          if (!isset($recipients[$key]['CLICK_GROUPS'][$tsGrouped])) {
            $recipients[$key]['CLICK_GROUPS'][$tsGrouped] = [0, 0]; // clicks, bot score
          }

          $recipients[$key]['CLICK_GROUPS'][$tsGrouped][0]++;
          $recipients[$key]['CLICK_GROUPS'][$tsGrouped][1] += (int) $click['bot_score'];
        }
      }

    }

    $launchInfo = [
      'recipients' => $recipients,
      'recentlyContacted' => []
    ];

    $emailsInEmail = $mRecipient->record->where('id_email', $idEmail)->pluck('email');

    $recentlyContacted = $mRecipient->record
      ->where('id_email', '!=', $idEmail)
      ->whereNotNull('id_mail')
      ->whereIn('email', $emailsInEmail)
      ->whereHas('MAIL', function($q) use ($recentlyContactedPeriod) {
        return $q->where('datetime_sent', '>=', date('Y-m-d H:i:s', strtotime('-' . $recentlyContactedPeriod . ' month')));
      })
      ->with('EMAIL')
      ->with('CONTACT.VALUES')
      ->get()
    ;

    foreach ($recentlyContacted as $tmp) {
      $launchInfo['recentlyContacted'][$tmp->email] = [
        'emailId' => $tmp->EMAIL->id,
        'emailsubject' => $tmp->EMAIL->subject,
        'mailSent' => $tmp->MAIL->datetime_sent,
      ];
    }

    return $launchInfo;
  }
}
