<?php

namespace HubletoMain\Core\Api;

use Exception;

class GetTemplateChartData extends \HubletoMain\Core\Controllers\ApiController
{
  public const OPERATIONS = [
    1 => "=",
    2 => "!=",
    3 => ">",
    4 => "<",
    5 => "LIKE",
    6 => "BETWEEN",
  ];

  public function renderJson(): ?array
  {
    $config = $this->main->urlParamAsArray("config");
    $model = $this->main->getModel($this->main->urlParamAsString("model"));

    $groupBy = $config["groupsBy"][0]["field"];
    $returnWith = (array) $config["returnWith"];

    $returnData = [];

    try {
      $operation = $returnWith[0]["type"];

      $function = "";
      switch ($operation) {
        case "count":
          $function = "COUNT(".$returnWith[0]["field"].")";
          break;
        case "average":
          $function = "AVG(".$returnWith[0]["field"].")";
          break;
        case "total":
          $function = "SUM(".$returnWith[0]["field"].")";
          break;
      }

      $query = $model->record->selectRaw($function." as result, ".$groupBy);
      foreach ((array) $config["searchGroups"] as $searchGroup) {
        if ($searchGroup["option"] == 5) {
          $query = $query->where($searchGroup["fieldName"], $this::OPERATIONS[$searchGroup["option"]], '%'.$searchGroup["value"].'%');
        } elseif ($searchGroup["option"] == 6) {
          $query = $query->whereBetween($searchGroup["fieldName"], [$searchGroup["value"], $searchGroup["value2"]]);
        } else {
          $query = $query->where($searchGroup["fieldName"], $this::OPERATIONS[$searchGroup["option"]], $searchGroup["value"]);
        }
      }

      $data = $query->groupBy($groupBy)->get()->toArray();

      $groupByModel = $this->main->getModel($model->getColumn($groupBy)->jsonSerialize()["model"]);
      $groupByModelLookupSqlValue = $groupByModel->lookupSqlValue;
      $groupByModelLookupSqlValue = str_replace("{%TABLE%}.", "", $groupByModelLookupSqlValue);

      if (empty($data)) {
        $returnData["labels"] = [];
        $returnData["values"] = [];
        $returnData["colors"] = [];
      } else {
        foreach ($data as $value) {
          $label = $groupByModel->record
            ->selectRaw($groupByModelLookupSqlValue)
            ->where("id", $value[$groupBy])
            ->first()
            ->toArray()[$groupByModelLookupSqlValue]
          ;
          $returnData["labels"][] = $label;
          $returnData["values"][] = $value["result"];
          $returnData["colors"][] = $this->generateRandomColor();
        }
      }

    } catch (Exception $e) {
      return [
        "status" => "failed",
        "error" => $e
      ];
    }

    return [
      "data" => $returnData,
      "status" => "success"
    ];
  }

  public function generateRandomColor(): string
  {
    $r = rand(0, 255);
    $g = rand(0, 255);
    $b = rand(0, 255);
    return "rgb(" . $r . "," . $g . "," . $b . ")";
  }
}
