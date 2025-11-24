<?php

namespace Hubleto\App\Community\Documents\Models;

use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Varchar;

class Folder extends \Hubleto\Erp\Model
{
  public string $table = 'folders';
  public string $recordManagerClass = RecordManagers\Folder::class;
  public ?string $lookupSqlValue = '{%TABLE%}.name';
  public ?string $lookupUrlAdd = 'documents/folders/add';

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'uid' => (new Varchar($this, $this->translate('Uid')))->setRequired()->setReadonly()->setDefaultValue(\Hubleto\Framework\Helper::generateUuidV4())->setDefaultHidden(),
      'id_parent_folder' => (new Lookup($this, $this->translate("Parent folder"), Folder::class))->setRequired()->setDefaultValue($this->router()->urlParamAsInteger('idParentFolder')),
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

   public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['addButtonText'] = 'Add Folder';
    $description->show(['header', 'fulltextSearch', 'columnSearch']);
    $description->hide(['footer']);
    return $description;
  }
}
