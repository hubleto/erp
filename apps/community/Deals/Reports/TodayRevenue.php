<?php

namespace HubletoApp\Community\Deals\Reports;

use HubletoApp\Community\Deals\Models\Deal;

class TodayRevenue extends \HubletoMain\Core\Report {

  protected string $urlSlug = 'today-revenue';
  public string $name = 'Today\'s revenue';

  public function getReportData(): array
  {
    $data = [];

    $this->model = new Deal($this->main);
    $data["model"] = $this->model->fullName;
    $data["name"] = $this->name;
    $data['groupsBy'] = [
      ["field" => "id_company", "title" => "Company"],
    ];
    $data['returnWith'] = [
      "total" => [
        ["field" => "price", "title" => "Total price of deals"],
      ],
    ];
    $data['fields'] = ["date_created" => $this->model->getColumn("date_created")];

    $data["option"] = 3;
    $data["value"] = date("Y-m-d");;

    return $data;
  }
}
