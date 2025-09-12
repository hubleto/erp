<?php

namespace Hubleto\App\Community\Customers\Controllers\Api;

use Hubleto\App\Community\Customers\Models\Customer;
use Hubleto\App\Community\Customers\Models\CustomerActivity;

class LogActivity extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): ?array
  {
    $idCustomer = $this->router()->urlParamAsInteger("idCustomer");
    $activity = $this->router()->urlParamAsString("activity");
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
          'id_owner' => $this->getService(AuthProvider::class)->getUserId(),
        ]);
      }
    }

    return [
      "status" => "success",
      "idCustomer" => $idCustomer,
    ];
  }

}
