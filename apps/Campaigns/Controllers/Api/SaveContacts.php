<?php

namespace Hubleto\App\Community\Campaigns\Controllers\Api;

use Hubleto\App\Community\Contacts\Models\Contact;
use Hubleto\App\Community\Campaigns\Models\Recipient;

class SaveContacts extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): ?array
  {
    $idCampaign = $this->router()->urlParamAsInteger('idCampaign');
    $contactIds = $this->router()->urlParamAsArray('contactIds');

    /** @var Recipient */
    $mRecipient = $this->getService(Recipient::class);

    $recipients = $mRecipient->record->where('id_campaign', $idCampaign)->pluck('id_contact')?->toArray();
    if (!is_array($recipients)) $recipients = [];

    // pridam nove kontakty

    foreach ($contactIds as $idContact) {
      $idContact = (int) $idContact;
      if ($idContact <= 0) continue;
      if (!in_array($idContact, $recipients)) {
        $mRecipient->record->recordCreate([
          'id_campaign' => $idCampaign,
          'id_contact' => $idContact,
        ]);
      }
    }

    // zmazem, ktore treba zmazat

    foreach ($recipients as $idContact) {
      if (!in_array($idContact, $contactIds)) {
        $mRecipient->record
          ->where('id_campaign', $idCampaign)
          ->where('id_contact', $idContact)
          ->delete()
        ;
      }
    }

    $recipients = $mRecipient->record->where('id_campaign', $idCampaign)->get();

    return $recipients->toArray();
  }
}
