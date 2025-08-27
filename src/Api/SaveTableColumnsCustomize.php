<?php declare(strict_types=1);

namespace Hubleto\Erp\Api;

use Exception;

class SaveTableColumnsCustomize extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): ?array
  {
    try {

      /** @var array<string, mixed> */
      $columnsConfig = $this->getRouter()->urlParamAsArray("record");

      $model = $this->getModel($this->getRouter()->urlParamAsString("model"));
      $tag = $this->getRouter()->urlParamAsString("tag");
      $allColumnsConfig = @json_decode($model->configAsString('tableColumns'), true) ?? [];

      if (!$allColumnsConfig) {
        $allColumnsConfig[$tag] = [];
      }

      foreach ($columnsConfig as $colName => $column) {
        if (is_array($column)) {
          $allColumnsConfig[$tag][$colName] = (int) $column["is_hidden"];
        }
      }

      $this->getConfig()->save(
        "user/" . $this->getAuthProvider()->getUserId() . "/models/" . $model->fullName . "/tableColumns",
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
