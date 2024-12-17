<?php

namespace CeremonyCrmMod\Core\Services\Controllers\Api;

use CeremonyCrmMod\Core\Services\Models\Service;
use Exception;

class GetServicePrice extends \CeremonyCrmApp\Core\Controller {

  public function renderJson(): ?array
  {
    $mService = new Service($this->app);
    $service = null;

    try {
      $service = $mService->eloquent
        ->where("id", $this->app->params["serviceId"])
        ->first()
        ->toArray()
      ;
    } catch (Exception $e) {
      return [
        "status" => "failed",
        "error" => $e
      ];
    }

    return [
      "unit_price" => $service["price"],
      "status" => "success"
    ];
  }

}
