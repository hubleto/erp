<?php

namespace HubletoApp\Community\Leads\Models;

use HubletoApp\Community\Documents\Models\Document;
use HubletoApp\Community\Leads\Models\Lead;

use \ADIOS\Core\Db\Column\Lookup;

class LeadDocument extends \HubletoMain\Core\Model
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
      'id_lead' => (new Lookup($this, $this->translate('Lead'), Lead::class))->setRequired(),
      'id_document' => (new Lookup($this, $this->translate('Document'), Document::class))->setRequired(),
    ]));
  }

  public function describeTable(): \ADIOS\Core\Description\Table
  {
    $description = parent::describeTable();
    if ($this->main->urlParamAsInteger('idLead') > 0){
      $description->permissions = [
        'canRead' => $this->main->permissions->granted($this->fullName . ':Read'),
        'canCreate' => $this->main->permissions->granted($this->fullName . ':Create'),
        'canUpdate' => $this->main->permissions->granted($this->fullName . ':Update'),
        'canDelete' => $this->main->permissions->granted($this->fullName . ':Delete'),
      ];
      $description->columns = [];
      $description->ui = [];
    }

    return $description;
  }
}
