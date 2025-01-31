<?php

namespace HubletoApp\Community\Leads\Models;

class LeadStatus extends \HubletoMain\Core\Model
{
  public string $table = 'lead_statuses';
  public string $eloquentClass = Eloquent\LeadStatus::class;
  public ?string $lookupSqlValue = '{%TABLE%}.name';

  public function columnsLegacy(array $columns = []): array
  {
    return parent::columnsLegacy(array_merge($columns, [
      'name' => [
        'type' => 'varchar',
        'title' => $this->translate('Name'),
        'required' => true,
      ],
      'order' => [
        'type' => 'int',
        'title' => $this->translate('Order'),
        'required' => true,
      ],
      'color' => [
        'type' => 'color',
        'title' => $this->translate('Color'),
        'required' => false,
      ],
    ]));
  }

  public function tableDescribe(array $description = []): array
  {
    $description["model"] = $this->fullName;
    $description = parent::tableDescribe($description);
    $description['ui']['title'] = 'Lead Statuses';
    $description['ui']['addButtonText'] = 'Add Lead Status';
    $description['ui']['showHeader'] = true;
    $description['ui']['showFooter'] = false;
    return $description;
  }
}
