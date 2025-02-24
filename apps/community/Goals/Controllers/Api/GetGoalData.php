<?php

namespace HubletoApp\Community\Goals\Controllers\Api;

use HubletoApp\Community\Deals\Models\Deal;

class GetGoalData extends \HubletoMain\Core\Controller
{
  public function renderJson(): ?array
  {
    $apiInterval = new GetIntervalData($this->main);

    $interval = $this->main->urlParamAsArray("interval");
    $idUser =  $this->main->urlParamAsInteger("user");
    $frequency =  $this->main->urlParamAsInteger("frequency");
    $idPipeline =  $this->main->urlParamAsInteger("idPipeline");
    $metric =  $this->main->urlParamAsInteger("metric");
    $goal =  $this->main->urlParamAsFloat("value");
    $goals =  $this->main->urlParamAsArray("values");

    switch ($frequency) {
      case 1:
        $weeks = $apiInterval->getWeeks($interval[0], $interval[1]);
        $data = $this->getData($weeks, $metric, $idPipeline, $idUser, $goal, $goals);
        return [
          "status" => "success",
          "data" => $data,
        ];
      case 2:
        $months = $apiInterval->getMonths($interval[0], $interval[1]);
        $data = $this->getData($months, $metric, $idPipeline, $idUser, $goal, $goals);
        return [
          "status" => "success",
          "data" => $data,
        ];
      case 3:
        $years = $apiInterval->getYears($interval[0], $interval[1]);
        $data = $this->getData($years, $metric, $idPipeline, $idUser, $goal, $goals);
        return [
          "status" => "success",
          "data" => $data,
        ];
      default:
        return [];
    }
  }

  function getData(array $intervals, int $metric, int $idPipeline, int $idUser, float $goal, $goals) {

    $mDeal = new Deal($this->main);
    $dataArray = [
      "labels" => [],
      "values" => [],
      "goals" => [],
    ];

    foreach ($intervals as $interval) {
      $data = $mDeal->eloquent
        ->whereBetween("date_created", [$interval["date_start"], $interval["date_end"]])
        ->where("id_pipeline", $idPipeline)
        ->where("id_user", $idUser)
      ;

      if ($metric == 1) $data = $data->selectRaw("SUM(price) as value");
      else if ($metric == 2) $data = $data->selectRaw("COUNT(id) as value");

      $data = $data->get()->toArray();
      $data = reset($data);

      array_push($dataArray["labels"], $interval["key"]);
      array_push($dataArray["values"], $data["value"]);
      array_push($dataArray["goals"], $goal);
    }
    if (isset($goals) && !empty($goals)) {
      $dataArray["goals"] = $goals;
    }

    return $dataArray;
  }
}
