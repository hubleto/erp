<?php

namespace HubletoApp\Community\Documents\Models;

use Hubleto\Framework\Db\Column\File;
use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\Framework\Db\Column\Lookup;

class Document extends \Hubleto\Framework\Models\Model
{
  public string $table = 'documents';
  public string $recordManagerClass = RecordManagers\Document::class;
  public ?string $lookupSqlValue = '{%TABLE%}.name';

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'uid' => (new Varchar($this, $this->translate('Uid')))->setRequired()->setReadonly()->setDefaultValue(\Hubleto\Framework\Helper::generateUuidV4()),
      'id_folder' => (new Lookup($this, $this->translate("Folder"), Folder::class))->setRequired()->setDefaultValue($this->main->urlParamAsInteger('idFolder')),
      'name' => (new Varchar($this, $this->translate('Document name')))->setRequired(),
      'file' => (new File($this, $this->translate('File'))),
      'hyperlink' => (new Varchar($this, $this->translate('File Link'))),
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

  public function describeInput(string $columnName): \Hubleto\Framework\Description\Input
  {
    $description = parent::describeInput($columnName);
    switch ($columnName) {
      case 'hyperlink':
        $description->setReactComponent('InputHyperlink');
        break;
    }
    return $description;
  }

  public function onAfterCreate(array $savedRecord): array
  {
    $savedRecord = parent::onAfterCreate($savedRecord);

    if (isset($savedRecord["creatingForModel"]) && isset($savedRecord["creatingForId"])) {
      $mCrossDocument = $this->main->getModel($savedRecord["creatingForModel"]);
      $mCrossDocument->record->recordCreate([
        "id_lookup" => $savedRecord["creatingForId"],
        "id_document" => $savedRecord["id"]
      ]);
    }

    return $savedRecord;
  }

  public function onBeforeUpdate(array $record): array
  {
    $document = (array) $this->record->find($record["id"])->toArray();

    if (!isset($document["file"])) {
      return $record;
    }

    $prevFilename = ltrim((string) $document["file"], "./");
    if (file_exists($this->main->config->getAsString('uploadFolder') . "/" . $prevFilename)) {
      unlink($this->main->config->getAsString('uploadFolder') . "/" . $prevFilename);
    }

    return $record;
  }

  public function onBeforeDelete(int $id): int
  {
    $document = (array) $this->record->find($id)->toArray();

    if (!isset($document["file"])) {
      return $id;
    }

    $prevFilename = ltrim((string) $document["file"], "./");
    if (file_exists($this->main->config->getAsString('uploadFolder') . "/" . $prevFilename)) {
      unlink($this->main->config->getAsString('uploadFolder') . "/" . $prevFilename);
    }

    return $id;
  }
}
