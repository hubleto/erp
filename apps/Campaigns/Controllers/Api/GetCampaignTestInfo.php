<?php

namespace Hubleto\App\Community\Campaigns\Controllers\Api;

use Hubleto\App\Community\Campaigns\Models\Recipient;
use Hubleto\App\Community\Campaigns\Models\Campaign;
use Hubleto\App\Community\Campaigns\Lib;

class GetCampaignTestInfo extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {
    $idCampaign = $this->router()->urlParamAsInteger('idCampaign');
    $recentlyContactedPeriod = $this->router()->urlParamAsInteger('recentlyContactedPeriod', 3);

    $testInfo = ['warnings' => []];

    /** @var Campaign */
    $mCampaign = $this->getModel(Campaign::class);

    /** @var Recipient */
    $mRecipient = $this->getModel(Recipient::class);

    $recipients = $mRecipient->record
      ->where('id_campaign', $idCampaign)
      ->with('STATUS')
      ->with('CLICKS')
      ->get()
    ;

    $varsUsedInRecipients = [];
    foreach ($recipients as $recipient) {
      $vars = @json_decode($recipient->variables, true);
      if (is_array($vars)) {
        $varsUsedInRecipients = array_merge($varsUsedInRecipients, array_keys($vars));
        $varsUsedInRecipients = array_unique($varsUsedInRecipients);
      }
    }
    
    $testInfo['recipients'] = $recipients;
    $testInfo['varsUsedInRecipients'] = $varsUsedInRecipients;
    
    $campaign = $mCampaign->record
      ->where('id', $idCampaign)
      ->with('MAIL_ACCOUNT')
      ->with('MAIL_TEMPLATE')
      ->first()
    ;

    $emailsInCampaign = $mRecipient->record->where('id_campaign', $idCampaign)->pluck('email');

    $recentlyContacted = $mRecipient->record
      ->where('id_campaign', '!=', $idCampaign)
      ->whereNotNull('id_mail')
      ->whereIn('email', $emailsInCampaign)
      ->whereHas('MAIL', function($q) use ($recentlyContactedPeriod) {
        return $q->where('datetime_sent', '>=', date('Y-m-d H:i:s', strtotime('-' . $recentlyContactedPeriod . ' month')));
      })
      ->with('CAMPAIGN')
      ->with('CONTACT.VALUES')
      ->get()
    ;

    $testInfo['recentlyContacted'] = [];
    foreach ($recentlyContacted as $tmp) {
      $testInfo['recentlyContacted'][$tmp->email] = [
        'campaignId' => $tmp->CAMPAIGN->id,
        'campaignName' => $tmp->CAMPAIGN->name,
        'mailSent' => $tmp->MAIL->datetime_sent,
      ];
    }

    // if (!$campaign->MAIL_ACCOUNT) {
    //   $testInfo['warnings'][] = $this->translate('Mail template is not set.');
    // }

    if (empty($campaign->mail_subject) || empty($campaign->mail_body)) {
      $testInfo['warnings'][] = $this->translate('Mail has empty subject or body.');
    } else {
      $bodyHtml = $campaign->mail_body;

      if (!strpos($bodyHtml, '{{ botDetectorHiddenLink }}')) {
        $testInfo['warnings'][] = $this->translate('Mail does not contain {{ botDetectorHiddenLink }} placeholder.');
      }

      if (!strpos($bodyHtml, '{{ unsubscribeHref }}')) {
        $testInfo['warnings'][] = $this->translate('Mail does not contain {{ unsubscribeHref }} placeholder.');
      }

      if (!strpos($bodyHtml, '{{ viewInBrowserHref }}')) {
        $testInfo['warnings'][] = $this->translate('Mail does not contain {{ viewInBrowserHref }} placeholder.');
      }

      $allVarsUsedInTemplate = true;
      foreach ($varsUsedInRecipients as $var) {
        if (!strpos($bodyHtml, '{{ ' . $var . ' }}')) {
          $allVarsUsedInTemplate = false;
          break;
        }
      }

      if (!$allVarsUsedInTemplate) {
        $testInfo['warnings'][] = $this->translate('Recipients contain variables {{ variables }} but not all of them are used in template.', ['variables' => join(', ', $varsUsedInRecipients)]);
      }
    }


    return $testInfo;
  }
}
