<?php

namespace Hubleto\App\Community\Campaigns\Controllers\Api;

use Hubleto\App\Community\Contacts\Models\Contact;
use Hubleto\App\Community\Campaigns\Models\Recipient;

class SaveRecipientsFromContacts extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): ?array
  {
    $idCampaign = $this->router()->urlParamAsInteger('idCampaign');
    $contactIds = $this->router()->urlParamAsArray('contactIds');

    /** @var Recipient */
    $mRecipient = $this->getModel(Recipient::class);

    $recipients = $mRecipient->record->where('id_campaign', $idCampaign)->pluck('id_contact')?->toArray();
    if (!is_array($recipients)) $recipients = [];

    /** @var Contact */
    $mContact = $this->getModel(Contact::class);
    $contactsRaw = $mContact->record->whereIn('id', $contactIds)->with('VALUES')->get();
    $contacts = [];
    foreach ($contactsRaw as $contact) $contacts[$contact->id] = $contact;

    // pridam nove kontakty

    foreach ($contactIds as $idContact) {
      $idContact = (int) $idContact;
      if ($idContact <= 0) continue;
      if (!$contacts[$idContact]) continue;
      if (in_array($idContact, $recipients)) continue;

      $email = '';
      foreach ($contacts[$idContact]->VALUES as $value) {
        if ($value['type'] == 'email') {
          $email = $value->value;
          break;
        }
      }
      $mRecipient->record->recordCreate([
        'id_campaign' => $idCampaign,
        'id_contact' => $idContact,
        'email' => $email,
        'first_name' => $contacts[$idContact]->first_name,
        'last_name' => $contacts[$idContact]->last_name,
        'salutation' => $contacts[$idContact]->salutation,
      ]);
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
