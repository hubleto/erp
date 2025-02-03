<?php

namespace HubletoApp\Community\Settings\Models;

use \ADIOS\Core\Db\Column\Varchar;
use \ADIOS\Core\Db\Column\Lookup;
use \ADIOS\Core\Db\Column\Text;

class Setting extends \HubletoMain\Core\Model
{
  public string $table = 'settings';
  public string $eloquentClass = Eloquent\Setting::class;

  public function columns(array $columns = []): array
  {
    return parent::columns([
      'key' => (new Varchar($this, $this->translate("Key")))->setRequired(),
      'value' => (new Text($this, $this->translate("Value"))),
      'id_user' => (new Lookup($this, $this->translate("Only for user"), User::class, 'RESTRICT')),
    ]);
  }

  public function indexes(array $indexes = []): array
  {
    return parent::indexes([
      'key' => [
        'type' => 'unique',
        'columns' => [
          'key' => [
            'order' => 'asc',
          ],
        ],
      ],
    ]);
  }

  public function describeTable(): \ADIOS\Core\Description\Table
  {
    $description = parent::describeTable();

    $description->ui['title'] = 'Settings';
    $description->ui['addButtonText'] = 'Add Setting';
    $description->ui['showHeader'] = true;
    $description->ui['showFooter'] = false;

    return $description;
  }
}
