<?php

namespace CeremonyCrmMod\Sales\Leads\Models;

use CeremonyCrmMod\Core\Settings\Models\Label;
use CeremonyCrmMod\Sales\Leads\Models\Lead;

class LeadLabel extends \CeremonyCrmApp\Core\Model
{
  public string $table = 'lead_labels';
  public string $eloquentClass = Eloquent\LeadLabel::class;

  public array $relations = [
    'LEAD' => [ self::BELONGS_TO, Lead::class, 'id_lead', 'id' ],
    'LABEL' => [ self::BELONGS_TO, Label::class, 'id_label', 'id' ],
  ];

  public function columns(array $columns = []): array
  {
    return parent::columns(array_merge($columns, [
      'id_lead' => [
        'type' => 'lookup',
        'title' => 'Lead',
        'model' => 'CeremonyCrmMod/Sales/Leads/Models/Lead',
        'foreignKeyOnUpdate' => 'CASCADE',
        'foreignKeyOnDelete' => 'CASCADE',
        'required' => true,
      ],
      'id_label' => [
        'type' => 'lookup',
        'title' => 'Tag',
        'model' => 'CeremonyCrmMod/Core/Settings/Models/Label',
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
