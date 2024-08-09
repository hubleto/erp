<?php

namespace CeremonyCrmApp\Modules\Core\Sandbox\Models;

class TestModel1 extends \CeremonyCrmApp\Core\Model
{
  public string $fullTableSqlName = 'test_model_1';
  public string $table = 'test_model_1';
  public string $eloquentClass = Eloquent\TestModel1::class;
  public ?string $lookupSqlValue = "{%TABLE%}.id";

  public function columns(array $columns = []): array
  {
    return parent::columns([
      'location' => [
        'type' => 'varchar',
        'inputJSX' => 'InputMapPoint',
        'title' => 'Location',
        'show_column' => true
      ],
    ]);
  }

  // public function tableParams(array $params = []): array
  // {
  //   $params = parent::tableParams();
  //   $params['title'] = 'Profiles';
  //   return $params;
  // }

}
