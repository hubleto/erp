<?php

namespace CeremonyCrmMod\Deals\Models;

use CeremonyCrmMod\Settings\Models\Label;
use CeremonyCrmMod\Deals\Models\Deal;

class DealLabel extends \CeremonyCrmApp\Core\Model
{
  public string $table = 'deal_labels';
  public string $eloquentClass = Eloquent\DealLabel::class;

  public array $relations = [
    'DEAL' => [ self::BELONGS_TO, Deal::class, 'id_deal', 'id' ],
    'LABEL' => [ self::BELONGS_TO, Label::class, 'id_label', 'id' ],
  ];

  public function columns(array $columns = []): array
  {
    return parent::columns(array_merge($columns, [
      'id_deal' => [
        'type' => 'lookup',
        'title' => 'Deal',
        'model' => 'CeremonyCrmMod/Deals/Models/Deal',
        'foreignKeyOnUpdate' => 'CASCADE',
        'foreignKeyOnDelete' => 'CASCADE',
        'required' => true,
      ],
      'id_label' => [
        'type' => 'lookup',
        'title' => 'Tag',
        'model' => 'CeremonyCrmMod/Settings/Models/Label',
        'foreignKeyOnUpdate' => 'CASCADE',
        'foreignKeyOnDelete' => 'CASCADE',
        'required' => true,
      ],
    ]));
  }

  public function tableDescribe(array $description = []): array
  {
    $description["model"] = $this->fullName;
    $description = parent::tableDescribe($description);
    $description['title'] = 'Company Categories';
    return $description;
  }

}
