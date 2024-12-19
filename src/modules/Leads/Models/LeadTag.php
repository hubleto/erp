<?php

namespace CeremonyCrmMod\Leads\Models;

use CeremonyCrmMod\Settings\Models\Tag;
use CeremonyCrmMod\Leads\Models\Lead;

class LeadTag extends \CeremonyCrmApp\Core\Model
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
      'id_lead' => [
        'type' => 'lookup',
        'title' => 'Lead',
        'model' => \CeremonyCrmMod\Leads\Models\Lead::class,
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
    $description['title'] = 'Company Categories';
    return $description;
  }

}
