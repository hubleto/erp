<?php

namespace Hubleto\App\Community\Documents\Models;

use Hubleto\App\Community\Auth\Models\User;
use Hubleto\Framework\Db\Column\File as ColumnFile;
use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Integer;
use Hubleto\Framework\Db\Column\DateTime;

class DocumentVersion extends \Hubleto\Erp\Model
{
  public string $table = 'documents_versions';
  public string $recordManagerClass = RecordManagers\DocumentVersion::class;
  public ?string $lookupSqlValue = '{%TABLE%}.version';
  public ?string $lookupUrlAdd = 'documents/versions/add';
  public ?string $lookupUrlDetail = 'documents/versions/{%ID%}';

  public array $relations = [
    'DOCUMENT' => [ self::BELONGS_TO, Document::class, 'id_document', 'id'],
    'CREATED_BY' => [ self::BELONGS_TO, User::class, 'id_created_by', 'id'],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'uid' => (new Varchar($this, $this->translate('Uid')))->setRequired()->setReadonly()->setDefaultValue(\Hubleto\Framework\Helper::generateUuidV4()),
      'id_document' => (new Lookup($this, $this->translate("Document"), Document::class))->setRequired()->setReadonly(),
      'version' => (new Integer($this, $this->translate('Version')))->setReadonly()->setDefaultVisible(),
      'created_on' => (new DateTime($this, $this->translate('Created on')))->setReadonly()->setDefaultVisible(),
      'id_created_by' => (new Lookup($this, $this->translate("Created by"), User::class))->setDefaultVisible(),
      'file' => (new ColumnFile($this, $this->translate('File')))->setDefaultVisible(),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['title'] = '';
    $description->ui['addButtonText'] = $this->translate('Add document version');
    $description->show(['header', 'fulltextSearch', 'columnSearch', 'moreActionsButton']);
    $description->hide(['footer']);

    return $description;
  }

  public function onBeforeCreate(array $record): array
  {
    if (!isset($record['uid'])) {
      $record['uid'] = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex(random_bytes(16)), 4));
    }
    $record['created_on'] = date('Y-m-d H:i:s');
    $record['id_created_by'] = $this->authProvider()->getUserId();

    $maxVersion = (int) $this->record->where('id_document', $record['id_document'])->max('version');
    $record['version'] = $maxVersion + 1;

    return $record;
  }
  
  public function onAfterCreate(array $savedRecord): array
  {
    $savedRecord = parent::onAfterCreate($savedRecord);
    return $savedRecord;
  }

  public function onAfterUpdate(array $originalRecord, array $savedRecord): array
  {
    if ($originalRecord["file"] != $savedRecord["file"]) {
      $localFilename = ltrim((string) $originalRecord["file"], "./");
      $fullPath = $this->config()->getAsString('uploadFolder') . "/" . $localFilename;
      if (!is_dir($fullPath) && file_exists($fullPath)) {
        unlink($fullPath);
      }
    }

    return parent::onAfterUpdate($originalRecord, $savedRecord);
  }

  public function onBeforeDelete(int $id): int
  {
    $file = (array) $this->record->find($id)->toArray();

    $localFilename = ltrim((string) $file["file"], "./");
    $fullPath = $this->config()->getAsString('uploadFolder') . "/" . $localFilename;
    if (!is_dir($fullPath) && file_exists($fullPath)) {
      unlink($fullPath);
    }

    return $id;
  }

  public function getRelationsIncludedInLoadFormData(): array|null
  {
    return ['DOCUMENT'];
  }
}
