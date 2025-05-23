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

    $activities = $mCustomerActivity->record
      ->select("customer_activities.*", "activity_types.color", "activity_types.name as activity_type")
      ->leftJoin("activity_types", "activity_types.id", "=", "customer_activities.id_activity_type")
      ->where("date_start", ">=", $dateStart)
      ->where("date_start", "<=", $dateEnd)
      ->with('CUSTOMER')
      ->with('CONTACT')
    ;

    if ($idCustomer > 0) $activities = $activities->where("id_customer", $idCustomer);

    $activities = $activities->get();
    $events = [];

    foreach ($activities as $key => $activity) { //@phpstan-ignore-line

      $dStart = (string) $activity->date_start;
      $tStart = (string) $activity->time_start;
      $dEnd = (string) $activity->date_end;
      $tEnd = (string) $activity->time_end;

      $events[$key]['id'] = $activity->id;

      if ($tStart != '') $events[$key]['start'] = $dStart . " " . $tStart;
      else $events[$key]['start'] = $dStart;

      if ($dEnd != '') {
        if ($tEnd != '') $events[$key]['end'] = $dEnd . " " . $tEnd;
        else $events[$key]['end'] = $dEnd;
      } else if ($tEnd != '') {
        $events[$key]['end'] = $dStart . " " . $tEnd;
      }

      //fix for fullCalendar not showing the last date of an event longer than one day
      if ((!empty($dStart) && !empty($dEnd) && (strtotime($dEnd) > strtotime($dStart)))) {
        if (empty($tEnd) || empty($tStart)) $events[$key]['end'] = date("Y-m-d", strtotime("+ 1 day", strtotime($dEnd)));
      }

      $events[$key]['allDay'] = $activity->all_day == 1 || $tStart == null ? true : false;
      $events[$key]['title'] = $activity->subject;
      $events[$key]['backColor'] = $activity->color;
      $events[$key]['color'] = $this->color;
      $events[$key]['type'] = $activity->activity_type;
      $events[$key]['source'] = 'customers';
      $events[$key]['details'] = $activity->CUSTOMER->name . ($activity->CONTACT ? ', ' . $activity->CONTACT->first_name . ' ' . $activity->CONTACT->last_name : '');
    }

    return $events;
  }

}