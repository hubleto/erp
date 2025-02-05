<?php

namespace HubletoApp\Community\Customers\Controllers\Api;

use HubletoApp\Community\Customers\Models\Company;
use HubletoApp\Community\Customers\Models\Person;

class GetCompany extends \HubletoMain\Core\Controller
{

  public function renderJson(): ?array
  {

    $mCompany = new Company($this->main);
    $companies = null;
    $companyArray = [];

    $searchString = $this->main->params["search"] ?? "";

    try {
      $companies = $mCompany->eloquent->selectRaw("*, name as _LOOKUP");
      /**
       * The string needs to be at least two characters long for the search to activate
       * due to the lookup inputs not clearing the search parameter when empty
       */

      $search = $this->main->urlParamAsString("search");
      if (strlen($search) > 1) {
        $companies
          ->where("name", "LIKE", "%" . $search . "%")
          ->orWhere("tax_id", "LIKE", "%" . $search . "%")
          ->orWhere("company_id", "LIKE", "%" . $search . "%")
          ->orWhere("vat_id", "LIKE", "%" . $search . "%")
        ;
      }

      $companies = $companies->get()->toArray();
    } catch (\Exception $e) {
      return [
        "status" => "failed",
        "error" => $e
      ];
    }

    foreach ($companies as $company) { //@phpstan-ignore-line
      $companyArray[$company["id"]] = $company;
    }

    return $companyArray;
  }
}
