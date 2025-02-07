<?php

namespace HubletoApp\Community\Deals\Reports;

use HubletoApp\Community\Deals\Models\Deal;

class MyRevenue extends \HubletoMain\Core\Report {

  protected string $urlSlug = 'high-price';
  public string $name = 'Deals with high value';

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

    $data['fields'] = ["price" => $this->model->getColumn("price")];
    $data["option"] = 3;
    $data["value"] = 80;

    return $data;
  }
}
