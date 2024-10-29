<?php

namespace CeremonyCrmApp\Modules\Core\Documents\Models;

use CeremonyCrmApp\Modules\Core\Settings\Models\ActivityType;
use CeremonyCrmApp\Modules\Core\Settings\Models\User;

class Document extends \CeremonyCrmApp\Core\Model
{
  public string $table = 'documents';
  public string $eloquentClass = Eloquent\Document::class;
  public ?string $lookupSqlValue = '{%TABLE%}.name';

  public function columns(array $columns = []): array
  {
    return parent::columns(array_merge($columns, [
      "file" => [
        "title" => "File",
        "type" => "image",
        "required" => true,
      ],
      "name" => [
        "title" => "Document name",
        "type" => "varchar",
        "required" => true,
      ]
    ]));
  }

  public function tableDescribe(array $description = []): array
  {
    $description["model"] = $this->fullName;
    $description = parent::tableDescribe($description);
    $description['ui']['title'] = 'Documents';
    $description['ui']['addButtonText'] = 'Add Document';
    $description['ui']['showHeader'] = true;
    return $description;
  }

}
