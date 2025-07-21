<?php

namespace HubletoApp\Community\Customers;

class Calendar extends \HubletoApp\Community\Calendar\Calendar
{
  public array $calendarConfig = [
    "title" => "Customers",
    "addNewActivityButtonText" => "Add new activity linked to customer",
    "icon" => "fas fa-address-card",
    "formComponent" => "CustomersFormActivity",
  ];

  public function loadEvent(int $id): array
  {
    return $this->prepareLoadActivityQuery(new Models\CustomerActivity($this->main), $id)->first()?->toArray();
  }

  public function loadEvents(string $dateStart, string $dateEnd, array $filter = []): array
  {
    $idCustomer = $this->main->urlParamAsString('idCustomer');
    $mCustomerActivity = $this->main->di->create(Models\CustomerActivity::class);
    $activities = $this->prepareLoadActivitiesQuery($mCustomerActivity, $dateStart, $dateEnd, $filter)->with('CUSTOMER')->with('CONTACT');
    if ($idCustomer > 0) {
      $activities = $activities->where("customer_activities.id_customer", $idCustomer);
    }

    $events = $this->convertActivitiesToEvents(
      'customers',
      $activities->get()?->toArray(),
      function (array $activity) {
        $customer = $activity['CUSTOMER'] ?? [];
        $contact = $activity['CONTACT'] ?? [];
        $contactName = trim(($contact['first_name'] ?? '') . ' ' . ($contact['last_name'] ?? ''));

        return ($customer['name'] ?? '') . (empty($contactName) ? '' : ', ' . $contactName);
      }
    );

    return $events;
  }

}
