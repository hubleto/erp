<?php

namespace HubletoApp\Community\Deals\Models;

use Hubleto\Framework\Db\Column\Lookup;

class DealTag extends \Hubleto\Framework\Models\Model
{
  public string $table = 'cross_deal_tags';
  public string $recordManagerClass = RecordManagers\DealTag::class;

  public array $relations = [
    'DEAL' => [ self::BELONGS_TO, Deal::class, 'id_deal', 'id' ],
    'TAG' => [ self::BELONGS_TO, Tag::class, 'id_tag', 'id' ],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_deal' => (new Lookup($this, $this->translate('Deal'), Deal::class))->setRequired(),
      'id_tag' => (new Lookup($this, $this->translate('Tag'), Tag::class))->setRequired(),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['title'] = $this->translate('Deal Tags');
    return $description;
  }

}
