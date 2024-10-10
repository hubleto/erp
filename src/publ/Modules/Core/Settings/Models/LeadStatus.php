<?php

namespace CeremonyCrmApp\Modules\Core\Settings\Models;

class LeadStatus extends \CeremonyCrmApp\Core\Model
{
  public string $table = 'lead_statuses';
  public string $eloquentClass = Eloquent\LeadStatus::class;
  public ?string $lookupSqlValue = '{%TABLE%}.name';

  public function columns(array $columns = []): array
  {
    return parent::columns(array_merge($columns, [
      'name' => [
        'type' => 'varchar',
        'title' => 'Name',
        'required' => true,
      ],
    ]));
  }

  public function tableDescribe(array $description = []): array
  {
    $description = parent::tableDescribe();
    $description['title'] = 'Lead Statuses';
    return $description;
  }

}
