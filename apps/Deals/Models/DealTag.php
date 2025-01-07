<?php

namespace CeremonyCrmMod\Deals\Models;

use CeremonyCrmMod\Settings\Models\Tag;
use CeremonyCrmMod\Deals\Models\Deal;

class DealTag extends \CeremonyCrmApp\Core\Model
{
  public string $table = 'deal_tags';
  public string $eloquentClass = Eloquent\DealTag::class;

  public array $relations = [
    'DEAL' => [ self::BELONGS_TO, Deal::class, 'id_deal', 'id' ],
    'TAG' => [ self::BELONGS_TO, Tag::class, 'id_tag', 'id' ],
  ];

  public function columns(array $columns = []): array
  {
    return parent::columns(array_merge($columns, [
      'id_deal' => [
        'type' => 'lookup',
        'title' => 'Deal',
        'model' => \CeremonyCrmMod\Deals\Models\Deal::class,
        'foreignKeyOnUpdate' => 'CASCADE',
        'foreignKeyOnDelete' => 'CASCADE',
        'required' => true,
      ],
      'id_tag' => [
        'type' => 'lookup',
        'title' => 'Tag',
        'model' => \CeremonyCrmMod\Settings\Models\Tag::class,
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
    $description['title'] = 'Deal Tags';
    return $description;
  }

}
