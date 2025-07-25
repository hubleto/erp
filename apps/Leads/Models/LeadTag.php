<?php

namespace HubletoApp\Community\Leads\Models;

use Hubleto\Framework\Db\Column\Lookup;

class LeadTag extends \Hubleto\Framework\Models\Model
{
  public string $table = 'cross_lead_tags';
  public string $recordManagerClass = RecordManagers\LeadTag::class;

  public array $relations = [
    'LEAD' => [ self::BELONGS_TO, Lead::class, 'id_lead', 'id' ],
    'TAG' => [ self::BELONGS_TO, Tag::class, 'id_tag', 'id' ],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_lead' => (new Lookup($this, $this->translate('Lead'), Lead::class))->setRequired(),
      'id_tag' => (new Lookup($this, $this->translate('Tag'), Tag::class))->setRequired(),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['title'] = 'Customer Categories';
    return $description;
  }

}
