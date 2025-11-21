<?php

namespace Hubleto\App\Community\Documents\Models;

use Hubleto\Framework\Db\Column\File;
use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\Framework\Db\Column\Lookup;

class Document extends \Hubleto\Erp\Model
{
  public string $table = 'documents';
  public string $recordManagerClass = RecordManagers\Document::class;
  public ?string $lookupSqlValue = '{%TABLE%}.name';

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'uid' => (new Varchar($this, $this->translate('Uid')))->setRequired()->setReadonly()->setDefaultValue(\Hubleto\Framework\Helper::generateUuidV4()),
      'id_folder' => (new Lookup($this, $this->translate("Folder"), Folder::class))->setRequired()->setDefaultValue($this->router()->urlParamAsInteger('idFolder'))->setDefaultVisible(),
      'name' => (new Varchar($this, $this->translate('Document name')))->setRequired()->setDefaultVisible()->setIcon(self::COLUMN_NAME_DEFAULT_ICON),
      'file' => (new File($this, $this->translate('File')))->setDefaultVisible(),
      'hyperlink' => (new Varchar($this, $this->translate('File Link')))->setReactComponent('InputHyperlink'),
      'origin_link' => (new Varchar($this, $this->translate('Origin Link'))),
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

  public function onAfterCreate(array $savedRecord): array
  {
    $savedRecord = parent::onAfterCreate($savedRecord);
    return $savedRecord;
  }

  public function onAfterUpdate(array $originalRecord, array $savedRecord): array
  {
    if ($originalRecord["file"] != $savedRecord["file"]) {
      $localFilename = ltrim((string) $originalRecord["file"], "./");
      if (file_exists($this->config()->getAsString('uploadFolder') . "/" . $localFilename)) {
        unlink($this->config()->getAsString('uploadFolder') . "/" . $localFilename);
      }
    }

    return parent::onAfterUpdate($originalRecord, $savedRecord);
  }

  public function onBeforeDelete(int $id): int
  {
    $document = (array) $this->record->find($id)->toArray();

    $localFilename = ltrim((string) $document["file"], "./");
    if (file_exists($this->config()->getAsString('uploadFolder') . "/" . $localFilename)) {
      unlink($this->config()->getAsString('uploadFolder') . "/" . $localFilename);
    }

    return $id;
  }
}
