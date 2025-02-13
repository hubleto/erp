<?php

namespace HubletoMain\Core\Api;

use Exception;
use HubletoMain\Core\Model;

class GetTemplateChartData extends \HubletoMain\Core\Controller
{
  const OPERATIONS = [
    1 => "=",
    2 => "!=",
    3 => ">",
    4 => "<",
    5 => "LIKE",
  ];

  public function renderJson(): ?array
  {
    $config = $this->main->urlParamAsArray("config");
    $model = $this->main->getModel($this->main->urlParamAsString("model"));

    $groupBy = $config["groupsBy"][0]["field"];
    $typeOptions = $config["returnWith"];

    $returnData = [];

    try {
      $operation = array_key_first($typeOptions);

      $function = "";
      switch ($operation) {
        case "count":
          $function = "COUNT(".$typeOptions[$operation][0]["field"].")";
          break;
        case "average":
          $function = "AVG(".$typeOptions[$operation][0]["field"].")";
          break;
        case "total":
          $function = "SUM(".$typeOptions[$operation][0]["field"].")";
          break;
      }

      $query = $model->eloquent->selectRaw($function." as function, ".$groupBy);
      foreach ($config["searchGroups"] as $searchGroup) {
        if ($searchGroup["option"] == 5) $query = $query->where($searchGroup["fieldName"], GetTemplateChartData::OPERATIONS[$searchGroup["option"]], '%'.$searchGroup["value"].'%');
        else $query = $query->where($searchGroup["fieldName"], GetTemplateChartData::OPERATIONS[$searchGroup["option"]], $searchGroup["value"]);
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
          $label = $groupByModel->eloquent
            ->selectRaw($groupByModelLookupSqlValue)
            ->where("id", $value[$groupBy])
            ->first()
            ->toArray()[$groupByModelLookupSqlValue]
          ;
          $returnData["labels"][] = $label;
          $returnData["values"][] = $value["function"];
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

  public function generateRandomColor(): string {
    $r = rand(0,255);
    $g = rand(0,255);
    $b = rand(0,255);
    return "rgb(" . $r . "," . $g . "," . $b . ")";
  }
}
