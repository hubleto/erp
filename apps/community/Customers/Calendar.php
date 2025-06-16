<?php

namespace HubletoApp\Community\Customers;

class Calendar extends \HubletoApp\Community\Calendar\Calendar {

  public array $activitySelectorConfig = [
    "addNewActivityButtonText" => "Add new activity linked to customer",
    "icon" => "fas fa-address-card",
    "formComponent" => "CustomersFormActivity",
  ];

  public function loadEvents(string $dateStart, string $dateEnd, array $filter = []): array
  {
    $idCustomer = $this->main->urlParamAsInteger('idCustomer');
    $mCustomerActivity = new Models\CustomerActivity($this->main);
    $activities = $this->prepareLoadActivitiesQuery($mCustomerActivity, $dateStart, $dateEnd, $filter)->with('CUSTOMER')->with('CONTACT');
    if ($idCustomer > 0) $activities = $activities->where("customer_activities.id_customer", $idCustomer);

    $events = $this->convertActivitiesToEvents(
      'customers',
      $activities->get()?->toArray(),
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