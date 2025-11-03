<?php

namespace Hubleto\App\Community\Campaigns\Controllers\Api;


use Hubleto\App\Community\Campaigns\Models\Campaign;
use Hubleto\App\Community\Campaigns\Models\Recipient;
use Hubleto\App\Community\Mail\Models\Mail;
use Hubleto\App\Community\Campaigns\Lib;

class SendTestEmail extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {
    $idCampaign = $this->router()->urlParamAsInteger('idCampaign');
    $to = $this->router()->urlParamAsString('to');
    $variables = $this->router()->urlParamAsString('variables');

    if (empty($to)) throw new \Exception("Recipient must be provided.");
    if (!filter_var($to, FILTER_VALIDATE_EMAIL)) throw new \Exception("Recipient is not valid email address.");

    $result = [];

    try {

      /** @var Campaign */
      $mCampaign = $this->getModel(Campaign::class);

      /** @var Mail */
      $mMail = $this->getModel(Mail::class);

      $campaign = $mCampaign->record->prepareReadQuery()
        ->where('campaigns.id', $idCampaign)
        ->first()
      ;

      $user = $this->authProvider()->getUser();

      $bodyHtml = Lib::getMailPreview($campaign->toArray(), [
        'first_name' => $user['first_name'],
        'last_name' => $user['last_name'],
        'variables' => $variables,
      ]);

      $mMail->createAndSend([
        'subject' => $campaign->MAIL_TEMPLATE->subject,
        'body_html' => $bodyHtml,
        'id_account' => $campaign->id_mail_account,
        'from' => $campaign->MAIL_ACCOUNT->sender_email ?? '',
        'to' => $to,
        'reply_to' => $campaign->reply_to ?? '',
        'datetime_created' => date('Y-m-d H:i:s'),
      ]);

      return ['status' => 'success'];
    } catch (\Throwable $e) {
      return ['status' => 'error', 'message' => $e->getMessage()];
    }

    return $result;
  }
}
