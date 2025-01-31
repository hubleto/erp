<?php

namespace HubletoApp\Community\Leads\Models;

use HubletoApp\Community\Documents\Models\Document;
use HubletoApp\Community\Leads\Models\Lead;

class LeadDocument extends \HubletoMain\Core\Model
{
  public string $table = 'lead_documents';
  public string $eloquentClass = Eloquent\LeadDocument::class;

  public array $relations = [
    'LEAD' => [ self::BELONGS_TO, Lead::class, 'id_lead', 'id' ],
    'DOCUMENT' => [ self::BELONGS_TO, Document::class, 'id_document', 'id' ],
  ];

  public function columnsLegacy(array $columns = []): array
  {
    return parent::columnsLegacy(array_merge($columns, [
      'id_lead' => [
        'type' => 'lookup',
        'title' => 'Lead',
        'model' => 'HubletoApp/Community/Leads/Models/Lead',
        'foreignKeyOnUpdate' => 'CASCADE',
        'foreignKeyOnDelete' => 'CASCADE',
        'required' => true,
      ],
      'id_document' => [
        'type' => 'lookup',
        'title' => 'Document',
        'model' => 'HubletoApp/Community/Documents/Models/Document',
        'foreignKeyOnUpdate' => 'CASCADE',
        'foreignKeyOnDelete' => 'CASCADE',
        'required' => true,
      ],
    ]));
  }

  public function tableDescribe(array $description = []): array
  {
    $description = parent::tableDescribe();
    if ($this->main->urlParamAsInteger('idLead') > 0){
      $description['permissions'] = [
        'canRead' => $this->app->permissions->granted($this->fullName . ':Read'),
        'canCreate' => $this->app->permissions->granted($this->fullName . ':Create'),
        'canUpdate' => $this->app->permissions->granted($this->fullName . ':Update'),
        'canDelete' => $this->app->permissions->granted($this->fullName . ':Delete'),
      ];
      unset($description["columns"]);
      unset($description["ui"]);
    }

    return $description;
  }
}
