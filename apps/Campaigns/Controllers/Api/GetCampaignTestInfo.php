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

    if (!$campaign->MAIL_ACCOUNT) {
      $testInfo['warnings'][] = $this->translate('Mail template is not set.');
    }

    if (!$campaign->MAIL_TEMPLATE) {
      $testInfo['warnings'][] = $this->translate('Mail template is not set.');
    } else {
      $template = $campaign->MAIL_TEMPLATE;
      $bodyHtml = $template->body_html;

      if (!strpos($bodyHtml, '{{ botDetectorHiddenLink }}')) {
        $testInfo['warnings'][] = 'Mail template does not contain `{{ botDetectorHiddenLink }}` placeholder.';
      }

      if (!strpos($bodyHtml, '{{ unsubscribeHref }}')) {
        $testInfo['warnings'][] = 'Mail template does not contain `{{ unsubscribeHref }}` placeholder.';
      }

      if (!strpos($bodyHtml, '{{ viewInBrowserHref }}')) {
        $testInfo['warnings'][] = 'Mail template does not contain `{{ viewInBrowserHref }}` placeholder.';
      }

      $allVarsUsedInTemplate = true;
      foreach ($varsUsedInRecipients as $var) {
        if (!strpos($bodyHtml, '{{ ' . $var . ' }}')) {
          $allVarsUsedInTemplate = false;
          break;
        }
      }

      if (!$allVarsUsedInTemplate) {
        $testInfo['warnings'][] = 'Recipients contain variables `' . join(', ', $varsUsedInRecipients) . '` but not all of them are used in template.';
      }
    }


    return $testInfo;
  }
}
