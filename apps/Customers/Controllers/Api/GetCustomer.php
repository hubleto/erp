<?php

namespace HubletoApp\Community\Customers\Controllers\Api;

use HubletoApp\Community\Customers\Models\Customer;

class GetCustomer extends \HubletoMain\Controllers\ApiController
{
  public function renderJson(): ?array
  {
    $mCustomer = $this->main->di->create(Customer::class);
    $customers = null;
    $customerArray = [];

    try {
      $customers = $mCustomer->record->selectRaw("*, name as _LOOKUP");
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
      $customer['_URL_DETAIL'] = 'customers/' . $customer['id'];
      $customerArray[$customer["id"]] = $customer;
    }

    return $customerArray;
  }
}
