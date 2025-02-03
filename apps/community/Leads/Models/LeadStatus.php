<?php

namespace HubletoApp\Community\Leads\Models;

use \ADIOS\Core\Db\Column\Varchar;
use \ADIOS\Core\Db\Column\Integer;
use \ADIOS\Core\Db\Column\Color;

class LeadStatus extends \HubletoMain\Core\Model
{
  public string $table = 'lead_statuses';
  public string $eloquentClass = Eloquent\LeadStatus::class;
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
    $description->ui['title'] = 'Lead Statuses';
    $description->ui['addButtonText'] = 'Add Lead Status';
    $description->ui['showHeader'] = true;
    $description->ui['showFooter'] = false;
    return $description;
  }
}
