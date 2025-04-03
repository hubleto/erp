<?php

namespace HubletoApp\Community\Documents\Models;

use HubletoApp\Community\Customers\Models\CustomerDocument;
use HubletoApp\Community\Deals\Models\Deal;
use HubletoApp\Community\Leads\Models\LeadDocument;
use HubletoApp\Community\Deals\Models\DealDocument;
use HubletoApp\Community\Leads\Models\Lead;

use \ADIOS\Core\Db\Column\Varchar;
use \ADIOS\Core\Db\Column\File;

class Document extends \HubletoMain\Core\Model
{
  public string $table = 'documents';
  public string $eloquentClass = Eloquent\Document::class;
  public ?string $lookupSqlValue = '{%TABLE%}.name';

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'name' => (new Varchar($this, $this->translate('Document name')))->setRequired(),
      'file' => (new File($this, $this->translate('File'))),
      'hyperlink' => (new Varchar($this, $this->translate('File Link'))),
      'origin_link' => (new Varchar($this, $this->translate('Origin Link'))),
    ]);
  }

  public function describeTable(): \ADIOS\Core\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['title'] = 'Documents';
    $description->ui['addButtonText'] = 'Add Document';
    $description->ui['showHeader'] = true;
    $description->ui['showFulltextSearch'] = true;

    unset($description->columns["origin_link"]);

    return $description;
  }

  public function describeInput(string $columnName): \ADIOS\Core\Description\Input
  {
    $description = parent::describeInput($columnName);
    switch ($columnName) {
      case 'hyperlink':
        $description->setReactComponent('InputHyperlink');
      break;
    }
    return $description;
  }

  public function onAfterCreate(array $originalRecord, array $savedRecord): array
  {
    if (isset($originalRecord["creatingForModel"]) && isset($originalRecord["creatingForId"])) {
      $mCrossDocument = $this->main->getModel($originalRecord["creatingForModel"]);
      $mCrossDocument->eloquent->create([
        "id_lookup" => $originalRecord["creatingForId"],
        "id_document" => $savedRecord["id"]
      ]);
    }

    return $savedRecord;
  }

  public function onBeforeUpdate(array $record): array
  {
    $document = (array) $this->eloquent->find($record["id"])->toArray();

    if (!isset($document["file"])) return $record;

    $prevFilename = ltrim((string) $document["file"],"./");
    if (file_exists($this->main->config->getAsString('uploadDir') . "/" . $prevFilename)) {
      unlink($this->main->config->getAsString('uploadDir') . "/" . $prevFilename);
    }

    return $record;
  }

  public function onBeforeDelete(int $id): int
  {
    $document = (array) $this->eloquent->find($id)->toArray();

    if (!isset($document["file"])) return $id;

    $prevFilename = ltrim((string) $document["file"],"./");
    if (file_exists($this->main->config->getAsString('uploadDir') . "/" . $prevFilename)) {
      unlink($this->main->config->getAsString('uploadDir') . "/" . $prevFilename);
    }

    return $id;
  }
}
