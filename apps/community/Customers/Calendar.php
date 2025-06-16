<?php

namespace HubletoApp\Community\Customers;

use HubletoApp\Community\Customers\Models\CustomerActivity;

class Calendar extends \HubletoMain\Core\Calendar {

  public array $activitySelectorConfig = [
    "addNewActivityButtonText" => "Add new activity linked to customer",
    "icon" => "fas fa-address-card",
    "formComponent" => "CustomersFormActivity",
  ];

  public function loadEvents(string $dateStart, string $dateEnd): array
  {
    $idCustomer = $this->main->urlParamAsInteger('idCustomer');

    $mCustomerActivity = new CustomerActivity($this->main);

    $activities = $mCustomerActivity->record->prepareReadQuery()
      ->select("customer_activities.*", "activity_types.color", "activity_types.name as activity_type")
      ->leftJoin("activity_types", "activity_types.id", "=", "customer_activities.id_activity_type")
      ->where("date_start", ">=", $dateStart)
      ->where("date_start", "<=", $dateEnd)
      ->with('CUSTOMER')
      ->with('CONTACT')
    ;

    if ($idCustomer > 0) $activities = $activities->where("customer_activities.id_customer", $idCustomer);

    $activities = $activities->get()?->toArray();

    $events = $this->convertActivitiesToEvents(
      'customers',
      $activities,
      function(array $activity) {
        $customer = $activity['CUSTOMER'] ?? [];
        $contact = $activity['CONTACT'] ?? [];
        $contactName = trim(($contact['first_name'] ?? '') . ' ' . ($contact['last_name'] ?? ''));

        return ($customer['name'] ?? '') . (empty($contactName) ? '' : ', ' . $contactName);
      }
    );

    return $events;
  }

}