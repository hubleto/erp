<?php

namespace CeremonyCrmApp\Modules\Core\Customers\Models;

class ActivityCompany extends \CeremonyCrmApp\Core\Model
{
  public string $table = 'company_activities';
  public string $eloquentClass = Eloquent\ActivityCompany::class;

  public array $relations = [
    'COMPANY' => [ self::BELONGS_TO, Company::class, 'id_company', 'id' ],
    'ACTIVITY' => [ self::BELONGS_TO, Activity::class, 'id_activity', 'id' ],
  ];

  public function columns(array $columns = []): array
  {
    return parent::columns(array_merge($columns, [
      'id_company' => [
        'type' => 'lookup',
        'title' => 'Company',
        'model' => 'CeremonyCrmApp/Modules/Core/Customers/Models/Company',
        'foreignKeyOnUpdate' => 'CASCADE',
        'foreignKeyOnDelete' => 'CASCADE',
        'required' => true,
      ],
      'id_activity' => [
        'type' => 'lookup',
        'title' => 'Tag',
        'model' => 'CeremonyCrmApp/Modules/Core/Customers/Models/Activity',
        'foreignKeyOnUpdate' => 'CASCADE',
        'foreignKeyOnDelete' => 'CASCADE',
        'required' => true,
      ],
    ]));
  }
}
