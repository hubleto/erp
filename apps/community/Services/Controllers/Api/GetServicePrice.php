<?php

namespace HubletoApp\Community\Services\Controllers\Api;

use HubletoApp\Community\Services\Models\Service;
use Exception;

class GetServicePrice extends \HubletoMain\Core\Controller {
  public int $returnType = \ADIOS\Core\Controller::RETURN_TYPE_JSON;

  public function renderJson(): ?array
  {
    $mService = new Service($this->main);
    $service = null;

    if (!$this->main->isUrlParam("serviceId")) {
      return [
        "status" => "failed",
        "error" => "The searched service was not specified"
      ];
    }

    try {
      $service = (array) $mService->eloquent
        ->where("id", $this->main->urlParamAsInteger("serviceId"))
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
      "status" => "success",
      "unit_price" => $service["price"]
    ];
  }

}
