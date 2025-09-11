<?php

namespace Hubleto\App\Community\Orders;

class Calendar extends \Hubleto\App\Community\Calendar\Calendar
{
  public array $calendarConfig = [
    "title" => "Orders",
    "addNewActivityButtonText" => "Add new activity linked to order",
    "icon" => "fas fa-handshake",
    "formComponent" => "OrdersFormActivity"
  ];

  public function loadEvent(int $id): array
  {
    return $this->prepareLoadActivityQuery($this->getModel(Models\OrderActivity::class), $id)->first()?->toArray();
  }

  public function loadEvents(string $dateStart, string $dateEnd, array $filter = []): array
  {
    $idOrder = $this->router()->urlParamAsInteger('idOrder');
    $mOrderActivity = $this->getModel(Models\OrderActivity::class);
    $activities = $this->prepareLoadActivitiesQuery($mOrderActivity, $dateStart, $dateEnd, $filter)->with('ORDER.CUSTOMER');
    if ($idOrder > 0) {
      $activities = $activities->where("id_order", $idOrder);
    }

    $events = $this->convertActivitiesToEvents(
      'orders',
      $activities->get()?->toArray(),
      function (array $activity) {
        if (isset($activity['ORDER'])) {
          $order = $activity['ORDER'];
          $customer = $order['CUSTOMER'] ?? [];
          return 'Order ' . $order['identifier'] . ' ' . $order['title'] . (isset($customer['name']) ? ', ' . $customer['name'] : '');
        } else {
          return '';
        }
      }
    );

    return $events;
  }

}
