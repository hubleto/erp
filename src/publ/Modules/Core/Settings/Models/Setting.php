<?php

namespace CeremonyCrmApp\Modules\Core\Settings\Models;

class Setting extends \ADIOS\Core\Model {
  public string $fullTableSqlName = 'settings';
  public string $table = 'settings';
  public string $eloquentClass = Eloquent\Setting::class;

  public function columns(array $columns = []): array
  {
    return parent::columns([
      'key' => [
        'type' => 'varchar',
        'byte_size' => '250',
        'title' => 'Key',
        'show_column' => true
      ],
      'value' => [
        'type' => 'text',
        'interface' => 'plain_text',
        'title' => 'Value',
        'show_column' => true
      ],
    ]);
  }

  public function indexes(array $indexes = []) {
    return parent::indexes([
      "key" => [
        "type" => "unique",
        "columns" => [
          "key" => [
            "order" => "asc",
          ],
        ],
      ],
    ]);
  }

}
