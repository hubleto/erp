<?php

namespace Hubleto\App\Community\Projects\Controllers\Api;

use Exception;
use Hubleto\App\Community\Projects\Models\Project;
use Hubleto\App\Community\Projects\Models\ProjectOrder;
use Hubleto\App\Community\Orders\Models\Order;

class CreateFromOrder extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): ?array
  {
    $idOrder = $this->router()->urlParamAsInteger("idOrder");

    if ($idOrder <= 0) {
      return [
        "status" => "failed",
        "error" => "The order for converting was not set"
      ];
    }

    /** @var Order */
    $mOrder = $this->getModel(Order::class);

    /** @var Project */
    $mProject = $this->getModel(Project::class);

    /** @var ProjectOrder */
    $mProjectOrder = $this->getModel(ProjectOrder::class);

    try {
      $order = $mOrder->record->prepareReadQuery()->where($mOrder->table . ".id", $idOrder)->first();
      if (!$order) {
        throw new Exception("Order was not found.");
      }

      $project = $mProject->record->recordCreate([
        "id_customer" => $order->id_customer,
        "title" => $order->title,
        "identifier" => $order->identifier,
      ]);

      $mProjectOrder->record->recordCreate([
        "id_project" => $project['id'],
        "id_order" => $order->id,
      ]);
    } catch (Exception $e) {
      return [
        "status" => "failed",
        "error" => $e
      ];
    }

    return [
      "status" => "success",
      "idProject" => $project['id'],
      "title" => str_replace(" ", "+", (string) $project['title'])
    ];
  }

}
