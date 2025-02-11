<?php

namespace HubletoApp\Community\Deals\Reports;

use HubletoApp\Community\Deals\Models\Deal;

class TodayRevenue extends \HubletoMain\Core\Report {

  protected string $urlSlug = 'deals-my-today-revenue';
  public string $name = 'My today\'s revenue';
  public string $modelClass = Deal::class;

  public function getReportConfig(): array
  {
    $config = [];

    $model = new Deal($this->main);
    // $config["model"] = $this->model->fullName;
    // $config["name"] = $this->name;
    $config['groupsBy'] = [
      ["field" => "id_user", "title" => "User"],
    ];
    $config['returnWith'] = [
      "total" => [
        ["field" => "price", "title" => "Total price of deals"],
      ],
    ];

    $config["searchGroups"] = [
      ["fieldName" => "id_user", "field" => $model->getColumn("id_user"), "option" => 1,  "value" => $this->main->auth->getUser()["id"],],
      ["fieldName" => "date_created", "field" => $model->getColumn("date_created"), "option" => 1,  "value" => date("Y-m-d")],
    ];

    return $config;
  }
}
