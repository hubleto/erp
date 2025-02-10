<?php

namespace HubletoApp\Community\Deals\Models;

use HubletoApp\Community\Settings\Models\Tag;
use HubletoApp\Community\Deals\Models\Deal;

use \ADIOS\Core\Db\Column\Lookup;

class DealTag extends \HubletoMain\Core\Model
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
      'id_deal' => (new Lookup($this, $this->translate('Deal'), Deal::class, 'CASCADE'))->setRequired(),
      'id_tag' => (new Lookup($this, $this->translate('Tag'), Tag::class))->setRequired(),
    ]));
  }

  public function describeTable(): \ADIOS\Core\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['title'] = 'Deal Tags';
    return $description;
  }

}
