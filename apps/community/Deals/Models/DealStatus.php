<?php

namespace HubletoApp\Community\Deals\Models;

use \ADIOS\Core\Db\Column\Varchar;
use \ADIOS\Core\Db\Column\Integer;
use \ADIOS\Core\Db\Column\Color;

class DealStatus extends \HubletoMain\Core\Model
{
  public string $table = 'deal_statuses';
  public string $eloquentClass = Eloquent\DealStatus::class;
  public ?string $lookupSqlValue = '{%TABLE%}.name';

  public function columns(array $columns = []): array
  {
    return parent::columns(array_merge($columns, [
      'name' => (new Varchar($this, $this->translate('Name')))->setRequired(),
      'order' => (new Integer($this, $this->translate('Order')))->setRequired(),
      'color' => new Color($this, $this->translate('Color')),
    ]));
  }

  public function describeTable(): \ADIOS\Core\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['title'] = 'Deal Statuses';
    $description->ui['addButtonText'] = 'Add Deal Status';
    $description->ui['showHeader'] = true;
    $description->ui['showFooter'] = false;
    return $description;
  }
}
