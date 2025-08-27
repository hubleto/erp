<?php

namespace Hubleto\App\Community\Documents\Models;

use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Varchar;

class Folder extends \Hubleto\Erp\Model
{
  public string $table = 'folders';
  public string $recordManagerClass = RecordManagers\Folder::class;
  public ?string $lookupSqlValue = '{%TABLE%}.name';

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'uid' => (new Varchar($this, $this->translate('Uid')))->setRequired()->setReadonly()->setDefaultValue(\Hubleto\Framework\Helper::generateUuidV4()),
      'id_parent_folder' => (new Lookup($this, $this->translate("Parent folder"), Folder::class))->setRequired()->setReadonly()->setDefaultValue($this->getRouter()->urlParamAsInteger('idParentFolder')),
      'name' => (new Varchar($this, $this->translate('Folder name')))->setRequired()->setCssClass('text-2xl text-primary'),
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
