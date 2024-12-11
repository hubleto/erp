<?php

namespace CeremonyCrmMod\Core\Customers\Models;

use CeremonyCrmMod\Core\Documents\Models\Document;

class CompanyDocument extends \CeremonyCrmApp\Core\Model
{
  public string $table = 'company_documents';
  public string $eloquentClass = Eloquent\CompanyDocument::class;

  public array $relations = [
    'COMPANY' => [ self::BELONGS_TO, Company::class, 'id_company', 'id' ],
    'DOCUMENT' => [ self::BELONGS_TO, Document::class, 'id_document', 'id' ],
  ];

  public function columns(array $columns = []): array
  {
    return parent::columns(array_merge($columns, [
      'id_company' => [
        'type' => 'lookup',
        'title' => 'Company',
        'model' => 'CeremonyCrmMod/Core/Customers/Models/Company',
        'foreignKeyOnUpdate' => 'CASCADE',
        'foreignKeyOnDelete' => 'CASCADE',
        'required' => true,
      ],
      'id_document' => [
        'type' => 'lookup',
        'title' => 'Document',
        'model' => 'CeremonyCrmMod/Core/Documents/Models/Document',
        'foreignKeyOnUpdate' => 'CASCADE',
        'foreignKeyOnDelete' => 'CASCADE',
        'required' => true,
      ],
    ]));
  }
}
