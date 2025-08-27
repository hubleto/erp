<?php

namespace HubletoApp\Community\Settings\Models;

use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Text;
use Hubleto\Framework\Db\Column\Varchar;

class Setting extends \HubletoMain\Model
{
  public string $table = 'settings';
  public string $recordManagerClass = RecordManagers\Setting::class;

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'key' => (new Varchar($this, $this->translate("Key")))->setRequired(),
      'value' => (new Text($this, $this->translate("Value"))),
      'id_owner' => (new Lookup($this, $this->translate("Only for user"), User::class)),
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

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();

    $description->ui['title'] = 'Settings';
    $description->ui['addButtonText'] = 'Add Setting';
    $description->ui['showHeader'] = true;
    $description->ui['showFulltextSearch'] = true;
    $description->ui['showColumnSearch'] = true;
    $description->ui['showFooter'] = false;

    return $description;
  }
}
