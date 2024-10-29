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
    $description["model"] = $this->fullName;
    $description = parent::tableDescribe($description);
    $description['ui']['title'] = 'Currencies';
    $description['ui']['addButtonText'] = 'Add currency';
    $description['ui']['showHeader'] = true;
    $description['ui']['showFooter'] = false;
    return $description;
  }

  public function formDescribe(array $description = []): array
  {
    $description = parent::formDescribe();
    $description['ui']['title'] = ($this->app->params['id'] == -1 ? "New currency" : "Currency");
    $description['ui']['subTitle'] = ($this->app->params['id'] == -1 ? "Adding" : "Editing");
    return $description;
  }

}
