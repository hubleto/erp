<?php

namespace HubletoApp\Leads\Models;

use HubletoApp\Customers\Models\Company;
use HubletoApp\Customers\Models\Person;
use HubletoApp\Leads\Models\Lead;
use HubletoApp\Settings\Models\Currency;
use HubletoApp\Settings\Models\User;

class LeadHistory extends \HubletoCore\Core\Model
{
  public string $table = 'lead_histories';
  public string $eloquentClass = Eloquent\LeadHistory::class;
  public ?string $lookupSqlValue = '{%TABLE%}.description';

  public array $relations = [
    'LEAD' => [ self::BELONGS_TO, Lead::class, 'id_lead','id'],
  ];

  public function columns(array $columns = []): array
  {
    return parent::columns(array_merge($columns, [

      'change_date' => [
        'type' => 'date',
        'title' => 'Change Date',
        'required' => true,
      ],
      'id_lead' => [
        'type' => 'lookup',
        'title' => 'Company',
        'model' => 'HubletoApp/Leads/Models/Lead',
        'foreignKeyOnUpdate' => 'CASCADE',
        'foreignKeyOnDelete' => 'CASCADE',
        'required' => true,
      ],
      'description' => [
        'type' => 'varchar',
        'title' => 'Description',
        'required' => true,
      ],
    ]));
  }

  public function tableDescribe(array $description = []): array
  {
    $description["model"] = $this->fullName;
    $description = parent::tableDescribe($description);
    $description['ui']['title'] = 'Leads';
    $description['ui']['addButtonText'] = 'Add Lead';
    $description['ui']['showHeader'] = true;
    $description['ui']['showFooter'] = false;
    unset($description['columns']['note']);
    return $description;
  }

}
