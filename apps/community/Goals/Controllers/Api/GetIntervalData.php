<?php

namespace HubletoApp\Community\Goals\Controllers\Api;

use HubletoApp\Community\Deals\Models\Deal;

class GetIntervalData extends \HubletoMain\Core\Controller
{
  public int $returnType = \ADIOS\Core\Controller::RETURN_TYPE_JSON;

  public function renderJson(): ?array
  {
    $interval = $this->main->urlParamAsArray("interval");
    $frequency =  $this->main->urlParamAsInteger("frequency");

    switch ($frequency) {
      case 1:
        $data = $this->getWeeks($interval[0], $interval[1]);
        return [
          "status" => "success",
          "data" => $data,
        ];
      case 2:
        $data = $this->getMonths($interval[0], $interval[1]);
        return [
          "status" => "success",
          "data" => $data,
        ];
      case 3:
        $data = $this->getYears($interval[0], $interval[1]);
        return [
          "status" => "success",
          "data" => $data,
        ];
      default:
        return [];
    }
  }

  function getWeeks($startDate, $endDate)
  {
    $start = strtotime("monday this week", strtotime($startDate));
    $end = strtotime("monday this week", strtotime($endDate));

    $weeks = [];

    while ($start <= $end) {
      $weekNumber = date('W', $start);

      // Get the start date of the week (Monday)
      $weekStart = strtotime("monday this week", $start);
      $weekStartFormatted = date('Y-m-d', $weekStart);

      // Get the end date of the week (Sunday)
      $weekEnd = strtotime("sunday this week", $weekStart);
      $weekEndFormatted = date('Y-m-d', $weekEnd);

      $weeks[] = [
        'key' => "Week " . $weekNumber,
        'date_start' => $weekStartFormatted,
        'date_end' => $weekEndFormatted
      ];

      // Move to the next week
      $start = strtotime("+1 week", $start);
    }

    // Replace the first week's start date with the initial start date
    $weeks[array_key_first($weeks)]['date_start'] = $startDate;

    // Replace the last week's end date with the initial end date
    $weeks[array_key_last($weeks)]['date_end'] = $endDate;

    return array_values($weeks); // Reset array keys for better structure
  }

  function getMonths($startDate, $endDate)
  {
    $start = strtotime(date("Y-m-01", strtotime($startDate)));
    $end = strtotime(date("Y-m-01", strtotime($endDate)));

    $months = [];

    while ($start <= $end) {
      $year = date("Y", $start);
      $monthFullString = date("F", $start);
      $monthStartFormatted  = date("Y-m-01", $start);
      $monthEndFormatted  = date("Y-m-t", $start);
      $months[] = [
        'key' => $monthFullString . " " . $year,
        'date_start' => $monthStartFormatted,
        'date_end' => $monthEndFormatted
      ];

      $start = strtotime("+1 month", $start);
    }

    // Replace the first months's start date with the initial start date
    $months[array_key_first($months)]['date_start'] = $startDate;

    // Replace the last months's end date with the initial end date
    $months[array_key_last($months)]['date_end'] = $endDate;

    return $months;
  }

  function getYears($startDate, $endDate)
  {
    $start = strtotime(date("Y-01-01", strtotime($startDate)));
    $end = strtotime(date("Y-01-01", strtotime($endDate)));

    $years = [];

    while ($start <= $end) {
      $year = date("Y", $start);
      $yearStartFormatted  = date("Y-01-01", $start);
      $yearEndFormatted  = date("Y-12-t", $start);
      $years[] = [
        'key' => "Year " . $year,
        'date_start' => $yearStartFormatted,
        'date_end' => $yearEndFormatted
      ];

      $start = strtotime("+1 year", $start);
    }

    // Replace the first year's start date with the initial start date
    $years[array_key_first($years)]['date_start'] = $startDate;

    // Replace the last year's end date with the initial end date
    $years[array_key_last($years)]['date_end'] = $endDate;

    return $years;
  }
}
