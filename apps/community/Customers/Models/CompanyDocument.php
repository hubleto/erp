<?php

namespace HubletoApp\Community\Customers\Models;

use HubletoApp\Community\Documents\Models\Document;

class CompanyDocument extends \HubletoMain\Core\Model
{
  public string $table = 'company_documents';
  public string $eloquentClass = Eloquent\CompanyDocument::class;

  public array $relations = [
    'COMPANY' => [ self::BELONGS_TO, Company::class, 'id_company', 'id' ],
    'DOCUMENT' => [ self::BELONGS_TO, Document::class, 'id_document', 'id' ],
  ];

  public function columnsLegacy(array $columns = []): array
  {
    return parent::columnsLegacy(array_merge($columns, [
      'id_company' => [
        'type' => 'lookup',
        'title' => 'Company',
        'model' => 'HubletoApp/Community/Customers/Models/Company',
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
    $description = parent::tableDescribe($description);

    if ($this->main->urlParamAsBool('idCompany') > 0) {
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
