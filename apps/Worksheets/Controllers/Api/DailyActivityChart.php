<?php

namespace HubletoApp\Community\Worksheets\Controllers\Api;

use Illuminate\Database\Capsule\Manager as DB;

class DailyActivityChart extends \HubletoMain\Controllers\ApiController
{
  public function response(): array
  {
    // $randomData = [];
    // $y = rand(10, 20);
    // for ($i = 100; $i > 0; $i--) {
    //   $randomData[] = ['x' => date("Y-m-d", strtotime("-{$i} days")), 'y' => $y ];
    //   if (rand(0, 1) == 0) $y += rand(12, 18) / 10;
    //   else $y -= rand(10, 15) / 10;
    // }
    $mActivity = $this->main->di->create(\HubletoApp\Community\Worksheets\Models\Activity::class);
    $worked = $mActivity->record
      ->groupBy(DB::raw('date(datetime_created)'))
      ->selectRaw('sum(duration) as worked, date(datetime_created) as date')
      ->get()?->toArray()
    ;

    $data = [];
    foreach ($worked as $item) {
      $data[] = ['x' => $item['date'], 'y' => $item['worked']];
    }

    return [
      'options' => [
        'responsive' => true,
        'tension' => 0.5,
        'pointRadius' => 2,
        'pointBackgroundColor' => 'rgba(14, 55, 0, 0.5)',
        'scales' => [
          // 'yAxes' => [
          //   'ticks' => [ 'precision' => 4 ],
          // ],
          'xAxes' => [
            'type' => 'time',
            'time' => [ 'unit' => 'day', 'tooltipFormat' => 'MMM DD'],
          ]
        ],
      ],
      'data' => [
        'datasets' => [
          [
            'label' => 'Daily activity',
            'fill' => [ 'target' => 'origin', 'below' => 'rgba(255, 0, 0, 0.3)', 'above' => 'rgba(0, 255, 0, 0.3)' ],
            'data' => $data,
            'backgroundColor' => 'rgba(255, 99, 132, 100)',
          ],
        ],
      ],
    ];
  }

}
