<?php

namespace HubletoApp\Community\Customers\Controllers\Api;

use HubletoApp\Community\Customers\Models\Customer;
use HubletoApp\Community\Customers\Models\CustomerActivity;

class LogActivity extends \HubletoMain\Controllers\ApiController
{
  public function renderJson(): ?array
  {
    $idCustomer = $this->getRouter()->urlParamAsInteger("idCustomer");
    $activity = $this->getRouter()->urlParamAsString("activity");
    if ($idCustomer > 0 && $activity != '') {
      $mCustomer = $this->getService(Customer::class);
      $customer = $mCustomer->record->find($idCustomer)->first()?->toArray();

      if ($customer && $customer['id'] > 0) {
        $mCustomerActivity = $this->getService(CustomerActivity::class);
        $mCustomerActivity->record->recordCreate([
          'id_customer' => $idCustomer,
          'subject' => $activity,
          'date_start' => date('Y-m-d'),
          'time_start' => date('H:i:s'),
          'all_day' => true,
          'completed' => true,
          'id_owner' => $this->getAuthProvider()->getUserId(),
        ]);
      }
    }

    return [
      "status" => "success",
      "idCustomer" => $idCustomer,
    ];
  }

}
