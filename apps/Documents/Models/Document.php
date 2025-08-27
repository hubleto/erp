<?php

namespace HubletoApp\Community\Documents\Models;

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
      'id_folder' => (new Lookup($this, $this->translate("Folder"), Folder::class))->setRequired()->setDefaultValue($this->getRouter()->urlParamAsInteger('idFolder'))->setProperty('defaultVisibility', true),
      'name' => (new Varchar($this, $this->translate('Document name')))->setRequired()->setProperty('defaultVisibility', true),
      'file' => (new File($this, $this->translate('File')))->setProperty('defaultVisibility', true),
      'hyperlink' => (new Varchar($this, $this->translate('File Link')))->setReactComponent('InputHyperlink'),
      'origin_link' => (new Varchar($this, $this->translate('Origin Link'))),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['title'] = ''; // 'Documents';
    $description->ui['addButtonText'] = $this->translate('Add Document');
    $description->ui['showHeader'] = true;
    $description->ui['showFulltextSearch'] = true;
    $description->ui['showColumnSearch'] = true;

    unset($description->columns["origin_link"]);

    return $description;
  }

  public function onAfterCreate(array $savedRecord): array
  {
    $savedRecord = parent::onAfterCreate($savedRecord);
    return $savedRecord;
  }

  public function onBeforeDelete(int $id): int
  {
    $document = (array) $this->record->find($id)->toArray();

    $localFilename = ltrim((string) $document["file"], "./");
    if (file_exists($this->getConfig()->getAsString('uploadFolder') . "/" . $localFilename)) {
      unlink($this->getConfig()->getAsString('uploadFolder') . "/" . $localFilename);
    }

    return $id;
  }
}
