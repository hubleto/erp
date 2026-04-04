<?php

namespace Hubleto\App\Community\Documents\Models;

use Hubleto\Framework\Db\Column\File as ColumnFile;
use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Boolean;

class File extends \Hubleto\Erp\Model
{
  public string $table = 'files';
  public string $recordManagerClass = RecordManagers\File::class;
  public ?string $lookupSqlValue = '{%TABLE%}.name';
  public ?string $lookupUrlAdd = 'files/add';
  public ?string $lookupUrlDetail = 'files/{%ID%}';

  public array $relations = [
    'FOLDER' => [ self::BELONGS_TO, Folder::class, 'id_folder', 'id'],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'uid' => (new Varchar($this, $this->translate('Uid')))->setRequired()->setReadonly()->setDefaultValue(\Hubleto\Framework\Helper::generateUuidV4()),
      'id_folder' => (new Lookup($this, $this->translate("Folder"), Folder::class))->setRequired()->setDefaultValue($this->router()->urlParamAsInteger('idFolder'))->setDefaultVisible(),
      'name' => (new Varchar($this, $this->translate('File name')))->setRequired()->setDefaultVisible()->setIcon(self::COLUMN_NAME_DEFAULT_ICON),
      'file' => (new ColumnFile($this, $this->translate('File')))->setDefaultVisible(),
      'hyperlink' => (new Varchar($this, $this->translate('File Link')))->setReactComponent('InputHyperlink'),
      'origin_link' => (new Varchar($this, $this->translate('Origin Link'))),
      'is_public' => (new Boolean($this, $this->translate('Public')))->setDefaultVisible(),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['title'] = '';
    $description->ui['addButtonText'] = $this->translate('Add file');
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
    return ['FOLDER'];
  }
}
