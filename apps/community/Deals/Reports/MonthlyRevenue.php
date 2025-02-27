<?php

namespace HubletoApp\Community\Deals\Reports;

use HubletoApp\Community\Deals\Models\Deal;

class MonthlyRevenue extends \HubletoMain\Core\Report {

  protected string $urlSlug = 'month-revenue';
  public string $name = 'My revenue this month';
  public string $modelClass = Deal::class;

  public function getReportConfig(): array
  {
    $config = [];

    $model = new Deal($this->main);
    $config['groupsBy'] = [
      ["field" => "id_customer", "title" => "Customer"],
    ];
    $config['returnWith'] = [
      "total" => [
        ["field" => "price", "title" => "Total price of deals"],
      ],
    ];

    $config["searchGroups"] = [
      ["fieldName" => "id_user", "field" => $model->getColumn("id_user"), "option" => 1,  "value" => $this->main->auth->getUser()["id"],],
      ["fieldName" => "date_created", "field" => $model->getColumn("date_created"), "option" => 6,  "value" => date("Y-m-01"), "value2" => date('Y-m-t')],
    ];

    return $config;
  }
}
