<?php

namespace HubletoApp\Community\Customers\Controllers\Api;

use HubletoApp\Community\Customers\Models\Customer;
use HubletoApp\Community\Customers\Models\Person;

class GetCustomer extends \HubletoMain\Core\Controller
{

  public function renderJson(): ?array
  {

    $mCustomer = new Customer($this->main);
    $customers = null;
    $customerArray = [];

    $searchString = $this->main->params["search"] ?? "";

    try {
      $customers = $mCustomer->eloquent->selectRaw("*, name as _LOOKUP");
      /**
       * The string needs to be at least two characters long for the search to activate
       * due to the lookup inputs not clearing the search parameter when empty
       */

      $search = $this->main->urlParamAsString("search");
      if (strlen($search) > 1) {
        $customers
          ->where("name", "LIKE", "%" . $search . "%")
          ->orWhere("tax_id", "LIKE", "%" . $search . "%")
          ->orWhere("customer_id", "LIKE", "%" . $search . "%")
          ->orWhere("vat_id", "LIKE", "%" . $search . "%")
        ;
      }

      $customers = $customers->get()->toArray();
    } catch (\Exception $e) {
      return [
        "status" => "failed",
        "error" => $e
      ];
    }

    foreach ($customers as $customer) { //@phpstan-ignore-line
      $customerArray[$customer["id"]] = $customer;
    }

    return $customerArray;
  }
}
