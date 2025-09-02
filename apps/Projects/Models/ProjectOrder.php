<?php

namespace Hubleto\App\Community\Projects\Models;

use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\App\Community\Orders\Models\Order;

class ProjectOrder extends \Hubleto\Erp\Model
{
  public string $table = 'projects_orders';
  public string $recordManagerClass = RecordManagers\ProjectOrder::class;
  public ?string $lookupSqlValue = '{%TABLE%}.id';

  public array $relations = [
    'ORDER'   => [ self::BELONGS_TO, Order::class, 'id_order', 'id'],
    'PROJECT' => [ self::BELONGS_TO, Project::class, 'id_project', 'id'],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_project' => (new Lookup($this, $this->translate('Project'), Project::class))->setRequired(),
      'id_order' => (new Lookup($this, $this->translate('Order'), Order::class))->setRequired(),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();

    $description->ui['title'] = 'Order Projects';
    $description->ui["addButtonText"] = $this->translate("Add project");

    if ($this->router()->urlParamAsInteger('idOrder') > 0) {
      $description->columns = [];
      $description->inputs = [];
      $description->ui = [];
    }

    return $description;
  }
}
