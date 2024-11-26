<?php

namespace CeremonyCrmApp\Modules\Sales\Sales\Models;

use CeremonyCrmApp\Modules\Core\Documents\Models\Document;

class DealDocument extends \CeremonyCrmApp\Core\Model
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
      'id_deal' => [
        'type' => 'lookup',
        'title' => 'Deal',
        'model' => 'CeremonyCrmApp/Modules/Sales/Sales/Models/Deal',
        'foreignKeyOnUpdate' => 'CASCADE',
        'foreignKeyOnDelete' => 'CASCADE',
        'required' => true,
      ],
      'id_document' => [
        'type' => 'lookup',
        'title' => 'Document',
        'model' => 'CeremonyCrmApp/Modules/Core/Documents/Models/Document',
        'foreignKeyOnUpdate' => 'CASCADE',
        'foreignKeyOnDelete' => 'CASCADE',
        'required' => true,
      ],
    ]));
  }
}
