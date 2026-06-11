<?php

namespace Hubleto\App\Community\EmailMarketing\Controllers\Api;


use Hubleto\App\Community\EmailMarketing\Models\Email;
use Hubleto\App\Community\EmailMarketing\Models\EmailRecipient;
use Hubleto\App\Community\Mail\Models\Mail;
use Hubleto\App\Community\EmailMarketing\Lib;

class SendTestEmail extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {
    $idEmail = $this->router()->urlParamAsInteger('idEmail');
    $to = $this->router()->urlParamAsString('to');
    $variables = $this->router()->urlParamAsString('variables');

    if (empty($to)) throw new \Exception($this->translate("Recipient must be provided."));
    if (!filter_var($to, FILTER_VALIDATE_EMAIL)) throw new \Exception($this->translate("Recipient is not valid email address."));

    $result = [];

    try {

      /** @var Email */
      $mEmail = $this->getModel(Email::class);

      /** @var Mail */
      $mMail = $this->getModel(Mail::class);

      $email = $mEmail->record
        ->where('email_marketing_emails.id', $idEmail)
        ->with('SENDER_ACCOUNT')
        ->first()
      ;

      $user = $this->authProvider()->getUser();

      $bodyHtml = Lib::getMailPreview($email->toArray(), [
        'first_name' => $user['first_name'],
        'last_name' => $user['last_name'],
        'variables' => $variables,
      ]);

      $mMail->createAndSend([
        'subject' => $email->mail_subject,
        'body_html' => $bodyHtml,
        'id_account' => $email->id_sender_account,
        'from' => $email->SENDER_ACCOUNT->sender_email ?? '',
        'to' => $to,
        'reply_to' => $email->reply_to ?? '',
        'datetime_created' => date('Y-m-d H:i:s'),
      ]);

      return ['status' => 'success'];
    } catch (\Throwable $e) {
      return ['status' => 'error', 'message' => $e->getMessage()];
    }

    return $result;
  }
}
