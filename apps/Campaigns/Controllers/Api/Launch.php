<?php

namespace Hubleto\App\Community\Campaigns\Controllers\Api;

use Hubleto\App\Community\Campaigns\Models\Campaign;
use Hubleto\App\Community\Campaigns\Models\CampaignContact;
use Hubleto\App\Community\Mail\Models\Mail;
use Hubleto\App\Community\Campaigns\Lib;

class Launch extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): ?array
  {
    $idCampaign = $this->router()->urlParamAsInteger('idCampaign');

    $result = [];

    try {

      $mCampaign = $this->getModel(Campaign::class);
      $mCampaignContact = $this->getModel(CampaignContact::class);
      $mMail = $this->getModel(Mail::class);

      $campaign = $mCampaign->record->prepareReadQuery()
        ->where('campaigns.id', $idCampaign)
        ->with('CONTACTS.CONTACT.VALUES')
        ->first()
      ;

      if (!$campaign->is_approved) throw new \Exception('Campaign is not approved.');

      $sec = 0;

      foreach ($campaign->CONTACTS as $campaignContact) {

        $contact = $campaignContact->CONTACT;
        $bodyHtml = Lib::getMailPreview(
          $campaign->toArray(),
          $contact->toArray(),
        );

        foreach ($contact->VALUES as $value) {

          if (!filter_var($value->value, FILTER_VALIDATE_EMAIL)) continue;

          $mailData = [
            'subject' => $campaign->name,
            'body_html' => $bodyHtml,
            'id_account' => $campaign->id_mail_account,
            'from' => $campaign->MAIL_ACCOUNT->sender_email ?? '',
            'to' => $value->value,
            'datetime_created' => date('Y-m-d H:i:s'),
            'datetime_scheduled_to_send' => date('Y-m-d H:i:s', strtotime("+{$sec} seconds")),
          ];

          $sec += 10;

          if ($campaignContact->id_mail > 0) {
            $mMail->record->where('id', $campaignContact->id_mail)->update($mailData);
          } else {
            $mail = $mMail->record->recordCreate($mailData);

            $mCampaignContact->record
              ->where('id_campaign', $idCampaign)
              ->where('id_contact', $contact->id)
              ->update(['id_mail' => (int) $mail['id']])
            ;
          }

        }
      }

      return ['status' => 'success'];
    } catch (\Throwable $e) {
      return ['status' => 'error', 'message' => $e->getMessage()];
    }

    return $result;
  }
}
