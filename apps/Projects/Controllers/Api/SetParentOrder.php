<?php

namespace Hubleto\App\Community\Projects\Controllers\Api;

use Exception;
use Hubleto\App\Community\Projects\Models\Project;
use Hubleto\App\Community\Projects\Models\ProjectOrder;
use Hubleto\App\Community\Orders\Models\Order;

class SetParentOrder extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {
    $idProject = $this->router()->urlParamAsInteger("idProject");
    $idOrder = $this->router()->urlParamAsInteger("idOrder");

    if ($idProject <= 0 || $idOrder <= 0) {
      return [
        "status" => "failed",
        "error" => "The project or order not set."
      ];
    }

    /** @var ProjectOrder */
    $mProjectOrder = $this->getModel(ProjectOrder::class);

    try {
      $mProjectOrder->record->where('id_project', $idProject)->delete();
      $mProjectOrder->record->create([
        'id_project' => $idProject,
        'id_order' => $idOrder,
      ]);
    } catch (Exception $e) {
      return [
        "status" => "failed",
        "error" => $e
      ];
    }

    return [
      "status" => "success",
      "idProject" => $idProject,
    ];
  }

}
