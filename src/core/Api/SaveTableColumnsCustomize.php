<?php

namespace HubletoMain\Core\Api;

use Exception;

class SaveTableColumnsCustomize extends \HubletoMain\Core\Controllers\ApiController
{
  public function renderJson(): ?array
  {
    try {

      $columnsConfig = $this->main->urlParamAsArray("record");
      $model = $this->main->getModel($this->main->urlParamAsString("model"));
      $tag = $this->main->urlParamAsString("tag");
      $allColumnsConfig = @json_decode($model->getConfigAsString('tableColumns'), true) ?? [];

      if (!$allColumnsConfig) {
        $allColumnsConfig[$tag] = [];
      }

      foreach ($columnsConfig as $colName => $column) {
        $allColumnsConfig[$tag][$colName] = $column["is_hidden"];
      }

      $this->main->config->save("user/".$this->main->auth->getUserId()."/models/".$model->fullName."/tableColumns", json_encode($allColumnsConfig));

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
