<?php

namespace CeremonyCrmMod\Sales\Deals\Models;

use CeremonyCrmMod\Core\Customers\Models\Company;
use CeremonyCrmMod\Core\Customers\Models\Person;
use CeremonyCrmMod\Sales\Deals\Models\Deal;
use CeremonyCrmMod\Core\Settings\Models\Currency;
use CeremonyCrmMod\Core\Settings\Models\User;

class DealHistory extends \CeremonyCrmApp\Core\Model
{
  public string $table = 'deal_histories';
  public string $eloquentClass = Eloquent\DealHistory::class;
  public ?string $lookupSqlValue = '{%TABLE%}.description';

  public array $relations = [
    'DEAL' => [ self::BELONGS_TO, Deal::class, 'id_deal','id'],
  ];

  public function columns(array $columns = []): array
  {
    return parent::columns(array_merge($columns, [

      'change_date' => [
        'type' => 'date',
        'title' => 'Change Date',
        'required' => true,
      ],
      'id_deal' => [
        'type' => 'lookup',
        'title' => 'Company',
        'model' => 'CeremonyCrmMod/Sales/Deals/Models/Deal',
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
    $description['ui']['title'] = 'Deals';
    $description['ui']['addButtonText'] = 'Add Deal';
    $description['ui']['showHeader'] = true;
    $description['ui']['showFooter'] = false;
    unset($description['columns']['note']);
    return $description;
  }

}
