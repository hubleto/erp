<?php

namespace HubletoApp\Community\Leads\Models;

use HubletoApp\Community\Settings\Models\Tag;
use HubletoApp\Community\Leads\Models\Lead;

use \ADIOS\Core\Db\Column\Lookup;

class LeadTag extends \HubletoMain\Core\Model
{
  public string $table = 'lead_tags';
  public string $eloquentClass = Eloquent\LeadTag::class;

  public array $relations = [
    'LEAD' => [ self::BELONGS_TO, Lead::class, 'id_lead', 'id' ],
    'TAG' => [ self::BELONGS_TO, Tag::class, 'id_tag', 'id' ],
  ];

  public function columns(array $columns = []): array
  {
    return parent::columns(array_merge($columns, [
      'id_lead' => (new Lookup($this, $this->translate('Lead'), Lead::class))->setRequired(),
      'id_tag' => (new Lookup($this, $this->translate('Tag'), Tag::class))->setRequired(),
    ]));
  }

  public function tableDescribe(): \ADIOS\Core\Description\Table
  {
    $description = parent::tableDescribe();
    $description->ui['title'] = 'Company Categories';
    return $description;
  }

}
