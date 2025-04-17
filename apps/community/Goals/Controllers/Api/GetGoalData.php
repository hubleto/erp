<?php

namespace HubletoApp\Community\Goals\Controllers\Api;

use HubletoApp\Community\Deals\Models\Deal;

class GetGoalData extends \HubletoMain\Core\Controllers\Controller
{
  public int $returnType = \ADIOS\Core\Controller::RETURN_TYPE_JSON;

  public function renderJson(): ?array
  {
    $apiInterval = new GetIntervalData($this->main);

    $interval = $this->main->urlParamAsArray("interval");
    $idUser =  $this->main->urlParamAsInteger("user");
    $frequency =  $this->main->urlParamAsInteger("frequency");
    $idPipeline =  $this->main->urlParamAsInteger("idPipeline");
    $metric =  $this->main->urlParamAsInteger("metric");
    $goal =  $this->main->urlParamAsFloat("goal");
    $goals =  $this->main->urlParamAsArray("goals");

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
      "pending" => [],
      "won" => [],
      "goals" => [],
    ];

    foreach ($intervals as $interval) {

      //search for deals that are pending until the end interval
      $pendingValues = $mDeal->record
        ->where("deal_result", 2)
        ->where("date_created", "<", $interval["date_end"])
        ->where("id_pipeline", $idPipeline)
        ->where("id_user", $idUser)
      ;

      if ($metric == 1) $pendingValues = $pendingValues->selectRaw("SUM(price) as value");
      else if ($metric == 2) $pendingValues = $pendingValues->selectRaw("COUNT(id) as value");

      $pendingValues = $pendingValues->get()->toArray();
      $pendingValues = reset($pendingValues);

      //search for deals that were won within the interval
      $wonValues = $mDeal->record
        ->where("deal_result", 1)
        ->whereBetween("date_result_update", [$interval["date_start"], $interval["date_end"]])
        ->where("id_pipeline", $idPipeline)
        ->where("id_user", $idUser)
      ;

      if ($metric == 1) $wonValues = $wonValues->selectRaw("SUM(price) as value");
      else if ($metric == 2) $wonValues = $wonValues->selectRaw("COUNT(id) as value");

      $wonValues = $wonValues->get()->toArray();
      $wonValues = reset($wonValues);

      array_push($dataArray["labels"], $interval["key"]);
      array_push($dataArray["pending"], $pendingValues["value"] ?? 0) ;
      array_push($dataArray["won"], $wonValues["value"] ?? 0) ;
      array_push($dataArray["goals"], $goal);
    }
    if (isset($goals) && !empty($goals)) {
      $dataArray["goals"] = $goals;
    }

    return $dataArray;
  }
}
