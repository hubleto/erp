<?php

namespace HubletoApp\Community\Services\Controllers\Api;

use HubletoApp\Community\Services\Models\Service;
use Exception;

class GetServicePrice extends \HubletoMain\Core\Controller {

  public function renderJson(): ?array
  {
    $mService = new Service($this->main);
    $service = null;

    if (!isset($this->main->params["serviceId"])) {
      return [
        "status" => "failed",
        "error" => "The searched service was not specified"
      ];
    }

    try {
      $service = $mService->eloquent
        ->where("id", $this->main->params["serviceId"])
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
