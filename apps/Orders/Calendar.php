<?php

namespace Hubleto\App\Community\Orders;

class Calendar extends \Hubleto\App\Community\Calendar\Calendar
{

  public function getCalendarConfig(): array
  {
    return [
      'position' => 6,
      'color' => '#f50ab9',
      'title' => $this->translate('Orders'),
      'addNewActivityButtonText' => $this->translate('Add new activity linked to order'),
      'icon' => 'fas fa-handshake',
      'formComponent' => 'OrderCalendarActivityForm',
    ];
  }

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

    // events from calendar
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

    // expected next invoice for the order
    $mOrder = $this->getModel(Models\Order::class);
    $ordersQuery = $mOrder->record
      ->with('OWNER')
      ->with('MANAGER')
      ->where('date_next_invoice_expected', '>=', $dateStart)
      ->where('date_next_invoice_expected', '<=', $dateEnd)
      ->where('is_closed', false)
    ;

    if (isset($filter['idUser']) && $filter['idUser'] > 0) {
      $ordersQuery = $ordersQuery->where('id_manager', $filter['idUser']);
    }
    if (isset($filter['fOwnership']) && $filter["fOwnership"] == 1) {
      $ordersQuery = $ordersQuery->where('id_manager', $this->authProvider()->getUserId());
    }

    $orders = $ordersQuery->get();

    foreach ($orders as $order) {
      $events[] = [
        'id' => (int) ($order->id ?? 0),
        'start' => date("Y-m-d", strtotime($order->date_next_invoice_expected)),
        'end' => date("Y-m-d", strtotime($order->date_next_invoice_expected)),
        'allDay' => true,
        'title' => 'Invoice expected: ' . $order->identifier . ' ' . $order->title,
        'source' => 'orders',
        'id_owner' => $order->id_manager ?? $order->id_owner,
        'owner' => $order->MANAGER ? $order->MANAGER->nick : $order->OWNER?->nick,
        'completed' => false,
        'url' => 'orders/' . $order->id,
      ];
    }

    return $events;
  }

}
