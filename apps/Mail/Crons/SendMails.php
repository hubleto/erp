<?php

namespace Hubleto\App\Community\Mail\Crons;

use Hubleto\App\Community\Mail\Models\Mail;

class SendMails extends \Hubleto\Erp\Cron
{
  public string $schedulingPattern = '* * * * *';
  public int $maxMailsToSend = 10;


  public function run(): void
  {
    /** @var Mail */
    $mMail = $this->getModel(Mail::class);

    $maxMailsToSend = $this->maxMailsToSend;
    if ($maxMailsToSend > 30) $maxMailsToSend = 30;

    $mailsToSend = $mMail->record->prepareReadQuery()
      ->whereNull('datetime_sent')
      ->where('datetime_scheduled_to_send', '<=', date('Y-m-d H:i:s'))
      ->with('ACCOUNT')
      ->with('MAILBOX')
      ->limit($maxMailsToSend) // cron is launched each minute; send max 3 emails per minute
      ->get()
    ;

    $this->logger()->info('SendMails: found ' . $mailsToSend->count() . ' mails to send (maxMailsToSend = ' . $this->maxMailsToSend . ')');

    foreach ($mailsToSend as $mail) {
      try {
        $mMail->send($mail->toArray());
        $this->logger()->info('Email `' . $mail['subject'] . '` to `' . $mail['to'] . '` sent successfully.');
        sleep(3); // waiting to avoid spam blacklisting
      } catch (\Exception $e) {
        $this->logger()->error('Failed to send email `' . $mail['subject'] . '` to `' . $mail['to'] . '`: ' . $e->getMessage());
      }
    }

  }

}
