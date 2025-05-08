<?php

namespace HubletoApp\Community\Documents\Models;

use ADIOS\Core\Db\Column\Lookup;
use ADIOS\Core\Db\Column\Varchar;

class Folder extends \HubletoMain\Core\Models\Model
{
  public string $table = 'folders';
  public string $recordManagerClass = RecordManagers\Folder::class;
  public ?string $lookupSqlValue = '{%TABLE%}.name';

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'uid' => (new Varchar($this, $this->translate('Uid'))),
      'id_parent_folder' => (new Lookup($this, $this->translate("Parent folder"), Folder::class, 'CASCADE')),
      'name' => (new Varchar($this, $this->translate('Folder name')))->setRequired(),
    ]);
  }

  public function indexes(array $indexes = []): array
  {
    return parent::indexes([
      "uid" => [
        "type" => "unique",
        "columns" => [
          "uid" => [
            "order" => "asc",
          ],
        ],
      ],
    ]);
  }

}
