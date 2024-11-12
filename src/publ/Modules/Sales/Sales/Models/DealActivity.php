<?php

namespace CeremonyCrmApp\Modules\Sales\Sales\Models;

use CeremonyCrmApp\Modules\Core\Customers\Models\Activity;

class DealActivity extends \CeremonyCrmApp\Core\Model
{
  public string $table = 'deal_activities';
  public string $eloquentClass = Eloquent\DealActivity::class;

  public array $relations = [
    'DEAL' => [ self::BELONGS_TO, Deal::class, 'id_deal', 'id' ],
    'ACTIVITY' => [ self::BELONGS_TO, Activity::class, 'id_activity', 'id' ],
  ];

  public function columns(array $columns = []): array
  {
    return parent::columns(array_merge($columns, [
      'id_deal' => [
        'type' => 'lookup',
        'title' => 'Deal',
        'model' => 'CeremonyCrmApp/Modules/Sales/Sales/Models/Deal',
        'foreignKeyOnUpdate' => 'CASCADE',
        'foreignKeyOnDelete' => 'CASCADE',
        'required' => true,
      ],
      'id_activity' => [
        'type' => 'lookup',
        'title' => 'Activity',
        'model' => 'CeremonyCrmApp/Modules/Core/Customers/Models/Activity',
        'foreignKeyOnUpdate' => 'CASCADE',
        'foreignKeyOnDelete' => 'CASCADE',
        'required' => true,
      ],
    ]));
  }
}
