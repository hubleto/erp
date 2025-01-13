<?php

namespace HubletoApp\Community\Customers\Controllers\Api;

use HubletoApp\Community\Customers\Models\Person;
use Exception;

class GetCompanyContacts extends \HubletoMain\Core\Controller {

  public function renderJson(): ?array
  {
    $mPerson = new Person($this->main);
    $persons = null;
    $personArray = [];

    try {
      $persons = $mPerson->eloquent->selectRaw("*, CONCAT(first_name, ' ', last_name) as _LOOKUP");
      if ((int) $this->main->params["id_company"] > 0) {
        $persons = $persons->where("id_company", (int) $this->main->params["id_company"]);
      }
      if ($this->main->params["search"] != "") {
        $persons->whereRaw("CONCAT(first_name, ' ', last_name) LIKE '%".$this->main->params["search"]."%'");
      }

      $persons = $persons->get()->toArray();

    } catch (Exception $e) {
      return [
        "status" => "failed",
        "error" => $e
      ];
    }

    foreach ($persons as $person) {
      $personArray[$person["id"]] = $person;
    }

    return $personArray;
  }
}
