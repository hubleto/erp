<?php

namespace HubletoMain\Core\Api;

use Exception;
use HubletoMain\Core\Model;

class GetChartData extends \HubletoMain\Core\Controller
{
  public Model $model;

  const OPERATIONS = [
    1 => "=",
    2 => "!=",
    3 => ">",
    4 => "<",
    5 => "LIKE",
  ];

  public function renderJson(): ?array
  {
    $this->model = $this->main->getModel($this->main->urlParamAsString("model"));
    $field = $this->main->urlParamAsString("field") ?? null;
    $option = $this->main->urlParamAsInteger("option") ?? null;
    $value = $this->main->urlParamAsString("value") ?? "";
    $type = $this->main->urlParamAsString("type") ?? null;
    $typeOptions = $this->main->urlParamAsString("typeOptions") ?? null;
    $groupBy = $this->main->urlParamAsString("groupBy") ?? null;
    $returnData = [];

    try {
      $typeOptions = json_decode($typeOptions, true);
      $exType = explode("/",$type);

      $function = "";
      switch ($exType[0]) {
        case "count":
          $function = "COUNT(".$typeOptions[$exType[0]][(int) $exType[1]]["field"].")";
          break;
        case "average":
          $function = "AVG(".$typeOptions[$exType[0]][(int) $exType[1]]["field"].")";
          break;
        case "total":
          $function = "SUM(".$typeOptions[$exType[0]][(int) $exType[1]]["field"].")";
          break;
      }

      $data = $this->model->eloquent->selectRaw($function." as function, ".$groupBy);
      if ($option == 5) $data = $data->where($field, GetChartData::OPERATIONS[$option], '%'.$value.'%');
      else $data = $data->where($field, GetChartData::OPERATIONS[$option], $value);
      $data = $data->groupBy($groupBy)->get()->toArray();

      $groupByModel = $this->main->getModel($this->model->getColumn($groupBy)->jsonSerialize()["model"]);
      $groupByModelLookupSqlValue = $groupByModel->lookupSqlValue;
      $groupByModelLookupSqlValue = str_replace("{%TABLE%}.", "", $groupByModelLookupSqlValue);

      if (empty($data)) {
        $returnData["labels"] = [];
        $returnData["labels"] = [];
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
