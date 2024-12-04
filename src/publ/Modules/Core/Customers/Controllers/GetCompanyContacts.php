<?php

namespace CeremonyCrmApp\Modules\Core\Customers\Controllers;

use CeremonyCrmApp\Modules\Core\Customers\Models\Person;
use Exception;

class GetCompanyContacts extends \CeremonyCrmApp\Core\Controller {

  public function renderJson(): ?array
  {
    $mPerson = new Person($this->app);
    $persons = null;
    $personArray = [];

    try {
      $persons = $mPerson->eloquent->selectRaw("*, CONCAT(first_name, ' ', last_name) as _LOOKUP");
      if ((int) $this->app->params["id_company"] > 0) {
        $persons = $persons->where("id_company", (int) $this->app->params["id_company"]);
      }
      if ($this->app->params["search"] != "") {
        $persons->whereRaw("CONCAT(first_name, ' ', last_name) LIKE '%".$this->app->params["search"]."%'");
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
