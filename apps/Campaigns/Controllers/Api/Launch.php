<?php

namespace Hubleto\App\Community\Campaigns\Controllers\Api;

use Hubleto\App\Community\Campaigns\Models\Campaign;
use Hubleto\App\Community\Campaigns\Models\Recipient;
use Hubleto\App\Community\Mail\Models\Mail;
use Hubleto\App\Community\Campaigns\Lib;

class Launch extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {
    $idCampaign = $this->router()->urlParamAsInteger('idCampaign');

    $result = [];

    try {

      $mCampaign = $this->getModel(Campaign::class);
      $mRecipient = $this->getModel(Recipient::class);
      $mMail = $this->getModel(Mail::class);

      $campaign = $mCampaign->record->prepareReadQuery()
        ->where('campaigns.id', $idCampaign)
        ->with('MAIL_TEMPLATE')
        ->with('RECIPIENTS')
        ->first()
      ;

      if (!$campaign->id_mail_account) throw new \Exception('Campaign has not configured mail account to send emails from.');
      if (!$campaign->id_mail_template) throw new \Exception('Campaign has not configured mail template.');
      if (!$campaign->is_approved) throw new \Exception('Campaign is not approved.');

      $sec = 0;
      $createdMails = 0;

      foreach ($campaign->RECIPIENTS as $recipient) {

        $bodyHtml = Lib::getMailPreview(
          $campaign->toArray(),
          $recipient->toArray(),
        );

        if (!filter_var($recipient->email, FILTER_VALIDATE_EMAIL)) continue;

        $mailData = [
          'subject' => $campaign->MAIL_TEMPLATE->subject,
          'body_html' => $bodyHtml,
          'id_account' => $campaign->id_mail_account,
          'from' => $campaign->MAIL_ACCOUNT->sender_email ?? '',
          'to' => $recipient->email,
          'datetime_created' => date('Y-m-d H:i:s'),
          'datetime_scheduled_to_send' => date('Y-m-d H:i:s', strtotime("+{$sec} seconds")),
        ];

        $sec += 10;

        $mail = $mMail->record->recordCreate($mailData);
        $createdMails++;

        $mRecipient->record
          ->where('id', $recipient->id)
          ->update(['id_mail' => (int) $mail['id']])
        ;
      }

      $mCampaign->record->where('id', $idCampaign)->update([
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
