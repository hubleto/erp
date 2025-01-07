<?php

namespace CeremonyCrmMod\Leads\Models;

use CeremonyCrmMod\Documents\Models\Document;
use CeremonyCrmMod\Leads\Models\Lead;

class LeadDocument extends \CeremonyCrmApp\Core\Model
{
  public string $table = 'lead_documents';
  public string $eloquentClass = Eloquent\LeadDocument::class;

  public array $relations = [
    'LEAD' => [ self::BELONGS_TO, Lead::class, 'id_lead', 'id' ],
    'DOCUMENT' => [ self::BELONGS_TO, Document::class, 'id_document', 'id' ],
  ];

  public function columns(array $columns = []): array
  {
    return parent::columns(array_merge($columns, [
      'id_lead' => [
        'type' => 'lookup',
        'title' => 'Lead',
        'model' => 'CeremonyCrmMod/Leads/Models/Lead',
        'foreignKeyOnUpdate' => 'CASCADE',
        'foreignKeyOnDelete' => 'CASCADE',
        'required' => true,
      ],
      'id_document' => [
        'type' => 'lookup',
        'title' => 'Document',
        'model' => 'CeremonyCrmMod/Documents/Models/Document',
        'foreignKeyOnUpdate' => 'CASCADE',
        'foreignKeyOnDelete' => 'CASCADE',
        'required' => true,
      ],
    ]));
  }
}
