<?php

namespace Hubleto\App\Community\Documents\Models;

use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\Framework\Db\Column\Integer;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\DateTime;
use Hubleto\App\Community\Auth\Models\User;

class Document extends \Hubleto\Erp\Model
{
  public string $table = 'documents';
  public string $recordManagerClass = RecordManagers\Document::class;
  public ?string $lookupSqlValue = '{%TABLE%}.name';
  public ?string $lookupUrlAdd = 'documents/add';
  public ?string $lookupUrlDetail = 'documents/{%ID%}';

  public array $relations = [
    'DOCUMENT' => [ self::BELONGS_TO, Document::class, 'id_document', 'id'],
    'CREATED_BY' => [ self::BELONGS_TO, User::class, 'id_created_by', 'id'],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'uid' => (new Varchar($this, $this->translate('Uid')))->setRequired()->setReadonly()->setDefaultValue(\Hubleto\Framework\Helper::generateUuidV4()),
      'name' => (new Varchar($this, $this->translate('Document name')))->setRequired()->setDefaultVisible()->setIcon(self::COLUMN_NAME_DEFAULT_ICON),
      'model' => (new Varchar($this, $this->translate('Model')))->setReadonly()->setDefaultVisible(),
      'record_id' => (new Integer($this, $this->translate('RecordId')))->setReadonly()->setDefaultVisible(),
      'created_on' => (new DateTime($this, $this->translate('Created on')))->setReadonly()->setDefaultVisible(),
      'id_created_by' => (new Lookup($this, $this->translate("Created by"), User::class))->setDefaultVisible(),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['title'] = ''; // 'Documents';
    $description->ui['addButtonText'] = $this->translate('Add Document');
    $description->show(['header', 'fulltextSearch', 'columnSearch', 'moreActionsButton']);
    $description->hide(['footer']);

    unset($description->columns["origin_link"]);

    return $description;
  }


  public function onBeforeCreate(array $record): array
  {
    if (!isset($record['uid'])) {
      $record['uid'] = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex(random_bytes(16)), 4));
    }

    $record['created_on'] = date('Y-m-d H:i:s');
    $record['id_created_by'] = $this->authProvider()->getUserId();

    return $record;
  }
  
  public function onAfterCreate(array $savedRecord): array
  {
    $savedRecord = parent::onAfterCreate($savedRecord);
    return $savedRecord;
  }
}
