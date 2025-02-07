<?php

namespace HubletoApp\Community\Deals\Reports;

use HubletoApp\Community\Deals\Models\Deal;

class TodayRevenue extends \HubletoMain\Core\Report {

  protected string $urlSlug = 'my-revenue';
  public string $name = 'My revenue';

  public function getReportData(): array
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
    $data['fields'] = ["id_user" => $this->model->getColumn("id_user")];

    $data["option"] = 1;
    $data["value"] = 1;

    return $data;
  }
}
