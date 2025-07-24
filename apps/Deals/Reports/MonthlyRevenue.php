<?php

namespace HubletoApp\Community\Deals\Reports;

use HubletoApp\Community\Deals\Models\Deal;

class MonthlyRevenue extends \Hubleto\Framework\Report
{
  protected string $urlSlug = 'month-revenue';
  public string $name = 'My revenue this month';
  // public string $modelClass = Deal::class;

  public function getConfig(): array
  {
    $config = [];

    $model = $this->main->di->create(Deal::class);
    $config['groupsBy'] = [
      ["field" => "id_customer", "title" => "Customer"],
    ];
    $config['returnWith'] = [
      ["type" => "total", "field" => "price", "title" => "Total price of deals"]
    ];

    $config["searchGroups"] = [
      ["fieldName" => "id_owner", "field" => $model->getColumn("id_owner"), "option" => 1,  "value" => $this->main->auth->getUser()["id"],],
      ["fieldName" => "date_created", "field" => $model->getColumn("date_created"), "option" => 6,  "value" => date("Y-m-01"), "value2" => date('Y-m-t')],
    ];

    return $config;
  }

  public function loadData(): array
  {
    $model = $this->main->di->create(Deal::class);
    return $this->loadDataDefault($model);
  }

}
