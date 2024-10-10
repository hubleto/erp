<?php

namespace CeremonyCrmApp\Modules\Core\Settings\Models;

class Currency extends \CeremonyCrmApp\Core\Model
{
  public string $table = 'currencies';
  public string $eloquentClass = Eloquent\Currency::class;
  public ?string $lookupSqlValue = 'CONCAT({%TABLE%}.name ," ","(",{%TABLE%}.code,")")';

  public function columns(array $columns = []): array
  {
    return parent::columns([
      'name' => [
        'type' => 'varchar',
        'title' => 'Currency Name',
      ],
      'code' => [
        'type' => 'varchar',
        'byte_size' => '5',
        'title' => 'Currency Code',
      ],
    ]);
  }

  public function tableDescribe(array $description = []): array
  {
    $description = parent::tableDescribe();
    $description['ui']['title'] = 'Currencies';
    return $description;
  }

}
