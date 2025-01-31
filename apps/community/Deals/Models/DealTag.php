<?php

namespace HubletoApp\Community\Deals\Models;

use HubletoApp\Community\Settings\Models\Tag;
use HubletoApp\Community\Deals\Models\Deal;

class DealTag extends \HubletoMain\Core\Model
{
  public string $table = 'deal_tags';
  public string $eloquentClass = Eloquent\DealTag::class;

  public array $relations = [
    'DEAL' => [ self::BELONGS_TO, Deal::class, 'id_deal', 'id' ],
    'TAG' => [ self::BELONGS_TO, Tag::class, 'id_tag', 'id' ],
  ];

  public function columnsLegacy(array $columns = []): array
  {
    return parent::columnsLegacy(array_merge($columns, [
      'id_deal' => [
        'type' => 'lookup',
        'title' => 'Deal',
        'model' => \HubletoApp\Community\Deals\Models\Deal::class,
        'foreignKeyOnUpdate' => 'CASCADE',
        'foreignKeyOnDelete' => 'CASCADE',
        'required' => true,
      ],
      'id_tag' => [
        'type' => 'lookup',
        'title' => 'Tag',
        'model' => \HubletoApp\Community\Settings\Models\Tag::class,
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
