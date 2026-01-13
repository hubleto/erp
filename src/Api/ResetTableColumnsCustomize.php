<?php declare(strict_types=1);

namespace Hubleto\Erp\Api;

use Exception;


class ResetTableColumnsCustomize extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {
    try {

      $model = $this->getModel($this->router()->urlParamAsString("model"));

      $path = "user/" . $this->getService(\Hubleto\Framework\AuthProvider::class)->getUserId() . "/models/" . $model->fullName . "/tableColumns";

      $this->config()->delete($path);

    } catch (Exception $e) {
      return [
        "status" => "failed",
        "error" => $e
      ];
    }

    return [
      "status" => "success"
    ];
  }
}
