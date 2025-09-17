<?php

namespace Hubleto\App\Community\Mail\Crons;

use Hubleto\App\Community\Mail\Models\Mail;

class SendMails extends \Hubleto\Erp\Cron
{
  public string $schedulingPattern = '*/5 * * * *';


  public function run(): void
  {
    /** @var Mail */
    $mMail = $this->getModel(Mail::class);

    $mailsToSend = $mMail->record->prepareReadQuery()
      ->whereNull('datetime_sent')
      ->where('datetime_scheduled_to_send', '<=', date('Y-m-d H:i:s'))
      ->with('ACCOUNT')
      ->with('MAILBOX')
      ->get()
    ;

      $this->logger()->info('SendMails: ' . $mailsToSend->count() . ' mails to send.');

    foreach ($mailsToSend as $mail) {
      try {
        $mMail->send($mail->toArray());
        $this->logger()->info('Email `' . $mail['subject'] . '` to `' . $mail['to'] . '` sent successfully.');
        sleep(30); // waiting 30 sec to avoid spam blacklisting
      } catch (\Exception $e) {
        $this->logger()->error('Failed to send email `' . $mail['subject'] . '` to `' . $mail['to'] . '`: ' . $e->getMessage());
      }
    }

  }

}
