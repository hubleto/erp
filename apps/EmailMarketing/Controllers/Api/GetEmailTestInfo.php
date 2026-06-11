<?php

namespace Hubleto\App\Community\EmailMarketing\Controllers\Api;

use Hubleto\App\Community\EmailMarketing\Models\EmailRecipient;
use Hubleto\App\Community\EmailMarketing\Models\Email;

class GetEmailTestInfo extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {
    $idEmail = $this->router()->urlParamAsInteger('idEmail');
    $recentlyContactedPeriod = $this->router()->urlParamAsInteger('recentlyContactedPeriod', 3);

    $testInfo = ['warnings' => []];

    /** @var Email */
    $mEmail = $this->getModel(Email::class);

    /** @var EmailRecipient */
    $mEmailRecipient = $this->getModel(EmailRecipient::class);

    $recipients = $mEmailRecipient->record
      ->where('id_email', $idEmail)
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
    
    $email = $mEmail->record
      ->where('id', $idEmail)
      ->with('SENDER_ACCOUNT')
      ->first()
    ;

    $emailsInEmail = $mEmailRecipient->record->where('id_email', $idEmail)->pluck('email');

    $recentlyContacted = $mEmailRecipient->record
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

    $testInfo['recentlyContacted'] = [];
    foreach ($recentlyContacted as $tmp) {
      $testInfo['recentlyContacted'][$tmp->email] = [
        'emailId' => $tmp->EMAIL->id,
        'emailSubject' => $tmp->EMAIL->subject,
        'mailSent' => $tmp->MAIL->datetime_sent,
      ];
    }

    if (empty($email->mail_subject) || empty($email->mail_body)) {
      $testInfo['warnings'][] = $this->translate('Mail has empty subject or body.');
    } else {
      $bodyHtml = $email->mail_body;

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
