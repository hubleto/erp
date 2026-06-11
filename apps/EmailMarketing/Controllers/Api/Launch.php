<?php

namespace Hubleto\App\Community\EmailMarketing\Controllers\Api;

use Hubleto\App\Community\EmailMarketing\Models\Email;
use Hubleto\App\Community\EmailMarketing\Models\EmailRecipient;
use Hubleto\App\Community\Mail\Models\Mail;
use Hubleto\App\Community\EmailMarketing\Lib;

class Launch extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {
    $idEmail = $this->router()->urlParamAsInteger('idEmail');

    $result = [];

    try {

      $mEmail = $this->getModel(Email::class);
      $mEmailRecipient = $this->getModel(EmailRecipient::class);
      $mMail = $this->getModel(Mail::class);

      $email = $mEmail->record->prepareReadQuery()
        ->where('email_marketing_emails.id', $idEmail)
        ->with('RECIPIENTS')
        ->with('RECIPIENTS.STATUS')
        ->first()
      ;

      if (!$email->id_sender_account) throw new \Exception('Email has not configured sender account.');
      if (!$email->is_approved) throw new \Exception('Email is not approved.');

      $sec = 0;
      $createdMails = 0;

      foreach ($email->RECIPIENTS as $recipient) {

        $bodyHtml = Lib::getMailPreview(
          $email->toArray(),
          $recipient->toArray(),
        );

        if (!filter_var($recipient->email, FILTER_VALIDATE_EMAIL)) continue;
        if ($recipient->STATUS?->is_unsubscribed ?? false) continue;
        if ($recipient->STATUS?->is_invalid ?? false) continue;
        if ($recipient->id_mail > 0) continue;

        $mailData = [
          'subject' => $email->mail_subject,
          'body_html' => $bodyHtml,
          'id_account' => $email->id_sender_account,
          'from' => $email->SENDER_ACCOUNT->sender_email ?? '',
          'to' => $recipient->email,
          'datetime_created' => date('Y-m-d H:i:s'),
          'datetime_scheduled_to_send' => date('Y-m-d H:i:s', strtotime("+{$sec} seconds")),
        ];

        $sec += 10;

        $mail = $mMail->record->recordCreate($mailData);
        $createdMails++;

        $mEmailRecipient->record
          ->where('id', $recipient->id)
          ->update(['id_mail' => (int) $mail['id']])
        ;
      }

      $mEmail->record->where('id', $idEmail)->update([
        'id_launched_by' => $this->authProvider()->getUserId(),
        'datetime_launched' => date('Y-m-d H:i:s'),
      ]);

      return [
        'status' => 'success',
        'createdMails' => $createdMails,
      ];
    } catch (\Throwable $e) {
      return ['status' => 'error', 'message' => $e->getMessage()];
    }

    return $result;
  }
}
