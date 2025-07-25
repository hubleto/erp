<?php

namespace HubletoApp\Community\Contacts\Controllers\Api;

use Exception;
use HubletoApp\Community\Contacts\Models\Contact;

class GetCustomerContacts extends \HubletoMain\Controllers\ApiController
{
  public function renderJson(): ?array
  {
    $mContact = $this->main->di->create(Contact::class);
    $contacts = null;
    $contactArray = [];

    try {
      $contacts = $mContact->record->selectRaw("*, CONCAT(first_name, ' ', last_name) as _LOOKUP");
      if ($this->main->urlParamAsInteger("id_customer") > 0) {
        $contacts = $contacts->where("id_customer", (int) $this->main->urlParamAsInteger("id_customer"));
      }
      if (strlen($this->main->urlParamAsString("search")) > 1) {
        $contacts->whereRaw("CONCAT(first_name, ' ', last_name) LIKE '%".$this->main->urlParamAsString("search")."%'");
      }

      $contacts = $contacts->get()->toArray();

    } catch (Exception $e) {
      return [
        "status" => "failed",
        "error" => $e
      ];
    }

    foreach ($contacts as $contact) { //@phpstan-ignore-line
      $contact['_URL_DETAIL'] = 'contacts/' . $contact['id'];
      $contactArray[$contact["id"]] = $contact;
    }

    return $contactArray;
  }
}
