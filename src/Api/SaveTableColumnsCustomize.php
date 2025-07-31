<?php declare(strict_types=1);

namespace HubletoMain\Api;

use Exception;

class SaveTableColumnsCustomize extends \HubletoMain\Controllers\ApiController
{
  public function renderJson(): ?array
  {
    try {

      /** @var array<string, mixed> */
      $columnsConfig = $this->main->urlParamAsArray("record");

      $model = $this->main->getModel($this->main->urlParamAsString("model"));
      $tag = $this->main->urlParamAsString("tag");
      $allColumnsConfig = @json_decode($model->getConfigAsString('tableColumns'), true) ?? [];

      if (!$allColumnsConfig) {
        $allColumnsConfig[$tag] = [];
      }

      foreach ($columnsConfig as $colName => $column) {
        if (is_array($column)) {
          $allColumnsConfig[$tag][$colName] = (int) $column["is_hidden"];
        }
      }

      $this->main->config->save(
        "user/" . $this->main->auth->getUserId() . "/models/" . $model->fullName . "/tableColumns",
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
