<?php declare(strict_types=1);

namespace Hubleto\Erp\Api;

use Exception;
use Hubleto\App\Community\Auth\AuthProvider;

class SaveTableColumnsCustomize extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): ?array
  {
    try {

      /** @var array<string, mixed> */
      $columnsConfig = $this->router()->urlParamAsArray("record");

      $model = $this->getModel($this->router()->urlParamAsString("model"));
      $tag = $this->router()->urlParamAsString("tag");
      $allColumnsConfig = @json_decode($model->configAsString('tableColumns'), true) ?? [];

      $allColumnsConfig[$tag] = [];

      foreach ($columnsConfig as $colName => $column) {
        if (is_array($column)) {
          $allColumnsConfig[$tag][$colName] = (int) $column["is_hidden"];
        }
      }

      $this->config()->save(
        "user/" . $this->getService(AuthProvider::class)->getUserId() . "/models/" . $model->fullName . "/tableColumns",
        (string) json_encode($allColumnsConfig)
      );

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
