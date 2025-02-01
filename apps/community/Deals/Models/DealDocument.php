<?php

namespace HubletoApp\Community\Deals\Models;

use HubletoApp\Community\Documents\Models\Document;

use \ADIOS\Core\Db\Column\Lookup;

class DealDocument extends \HubletoMain\Core\Model
{
  public string $table = 'deal_documents';
  public string $eloquentClass = Eloquent\DealDocument::class;

  public array $relations = [
    'DEAL' => [ self::BELONGS_TO, Deal::class, 'id_deal', 'id' ],
    'DOCUMENT' => [ self::BELONGS_TO, Document::class, 'id_document', 'id' ],
  ];

  public function columns(array $columns = []): array
  {
    return parent::columns(array_merge($columns, [
      'id_deal' => (new Lookup($this, $this->translate('Deal'), Deal::class, 'CASCADE'))->setRequired(),
      'id_document' => (new Lookup($this, $this->translate('Document'), Document::class, 'CASCADE'))->setRequired(),
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
