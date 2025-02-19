<?php

namespace HubletoApp\Community\Contacts\Controllers\Api;

use HubletoApp\Community\Contacts\Models\Person;
use Exception;

class GetCustomerContacts extends \HubletoMain\Core\Controller {

  public function renderJson(): ?array
  {
    $mPerson = new Person($this->main);
    $persons = null;
    $personArray = [];

    try {
      $persons = $mPerson->eloquent->selectRaw("*, CONCAT(first_name, ' ', last_name) as _LOOKUP");
      if ($this->main->urlParamAsInteger("id_customer") > 0) {
        $persons = $persons->where("id_customer", (int) $this->main->urlParamAsInteger("id_customer"));
      }
      if (strlen($this->main->urlParamAsString("search")) > 1) {
        $persons->whereRaw("CONCAT(first_name, ' ', last_name) LIKE '%".$this->main->urlParamAsString("search")."%'");
      }

      $persons = $persons->get()->toArray();

    } catch (Exception $e) {
      return [
        "status" => "failed",
        "error" => $e
      ];
    }

    foreach ($persons as $person) { //@phpstan-ignore-line
      $personArray[$person["id"]] = $person;
    }

    return $personArray;
  }
}
