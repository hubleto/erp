<?php

namespace Hubleto\App\Community\Contacts\Controllers\Api;

use Exception;
use Hubleto\App\Community\Contacts\Models\Contact;

class GetCustomerContacts extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): ?array
  {
    /** @var Contact */
    $mContact = $this->getModel(Contact::class);
    $contacts = null;
    $contactArray = [];

    try {
      $contacts = $mContact->record->selectRaw("*, CONCAT(first_name, ' ', last_name) as _LOOKUP");
      if ($this->router()->urlParamAsInteger("id_customer") > 0) {
        $contacts = $contacts->where("id_customer", (int) $this->router()->urlParamAsInteger("id_customer"));
      }
      if (strlen($this->router()->urlParamAsString("search")) > 1) {
        $contacts->whereRaw(
          "CONCAT(first_name, ' ', last_name) LIKE ?",
          [ '%".$this->router()->urlParamAsString("search")."%' ]
        );
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
