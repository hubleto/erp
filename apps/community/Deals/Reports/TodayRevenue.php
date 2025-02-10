<?php

namespace HubletoApp\Community\Deals\Reports;

use HubletoApp\Community\Deals\Models\Deal;

class TodayRevenue extends \HubletoMain\Core\Report {

  protected string $urlSlug = 'my-today-revenue';
  public string $name = 'My today\'s revenue';

  public function getReportConfig(): array
  {
    $data = [];

    $this->model = new Deal($this->main);
    $data["model"] = $this->model->fullName;
    $data["name"] = $this->name;
    $data['groupsBy'] = [
      ["field" => "id_user", "title" => "User"],
    ];
    $data['returnWith'] = [
      "total" => [
        ["field" => "price", "title" => "Total price of deals"],
      ],
    ];

    $data["searchGroups"] = [
      ["fieldName" => "id_user", "field" => $this->model->getColumn("id_user"), "option" => 1,  "value" => $this->main->auth->getUser()["id"],],
      ["fieldName" => "date_created", "field" => $this->model->getColumn("date_created"), "option" => 1,  "value" => date("Y-m-d")],
    ];

    return $data;
  }
}
