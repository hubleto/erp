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

  public array $relations = [
    'CUSTOMER_DOCUMENT' => [ self::HAS_ONE, CustomerDocument::class, 'id_document', 'id' ],
    'LEAD_DOCUMENT' => [ self::HAS_ONE, LeadDocument::class, 'id_document', 'id' ],
    'DEAL_DOCUMENT' => [ self::HAS_ONE, DealDocument::class, 'id_document', 'id' ],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'name' => (new Varchar($this, $this->translate('Document name')))->setRequired(),
      'file' => (new File($this, $this->translate('File'))),
      'hyperlink' => (new Varchar($this, $this->translate('Link'))),
    ]);
  }

  public function describeTable(): \ADIOS\Core\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['title'] = 'Documents';
    $description->ui['addButtonText'] = 'Add Document';
    $description->ui['showHeader'] = true;
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
    $mCustomerDocument = new CustomerDocument($this->main);
    $mLead = new Lead($this->main);
    $mDeal = new Deal($this->main);
    $mLeadDocument = new LeadDocument($this->main);
    $mDealDocument = new DealDocument($this->main);

    if (isset($savedRecord["creatingForModel"])) {
      if ($savedRecord["creatingForModel"] == "Customer") {
        $mCustomerDocument->eloquent->create([
          "id_document" => $savedRecord["id"],
          "id_customer" => $savedRecord["creatingForId"]
        ]);
      } else if ($savedRecord["creatingForModel"] == "Lead") {

        $lead = $mLead->eloquent->find($savedRecord["creatingForId"]);
        $mCustomerDocument->eloquent->create([
          "id_document" => $savedRecord["id"],
          "id_customer" => $savedRecord["creatingForId"]
        ]);
        $mLeadDocument->eloquent->create([
          "id_document" => $savedRecord["id"],
          "id_lead" => $lead->id
        ]);
      } else if ($savedRecord["creatingForModel"] == "Deal") {

        $deal = $mDeal->eloquent->find($savedRecord["creatingForId"]);
        $mCustomerDocument->eloquent->create([
          "id_document" => $savedRecord["id"],
          "id_customer" => $savedRecord["creatingForId"]
        ]);
        $mDealDocument->eloquent->create([
          "id_document" => $savedRecord["id"],
          "id_deal" => $deal->id
        ]);
      }
    }
    return $savedRecord;
  }

  public function onBeforeUpdate(array $record): array
  {
    $document = (array) $this->eloquent->find($record["id"])->toArray();

    if (!isset($document["file"])) return $record;

    $prevFilename = ltrim((string) $document["file"],"./");
    if (file_exists($this->main->configAsString('uploadDir') . "/" . $prevFilename)) {
      unlink($this->main->configAsString('uploadDir') . "/" . $prevFilename);
    }

    return $record;
  }

  public function onBeforeDelete(int $id): int
  {
    $document = (array) $this->eloquent->find($id)->toArray();

    if (!isset($document["file"])) return $id;

    $prevFilename = ltrim((string) $document["file"],"./");
    if (file_exists($this->main->configAsString('uploadDir') . "/" . $prevFilename)) {
      unlink($this->main->configAsString('uploadDir') . "/" . $prevFilename);
    }

    return $id;
  }
}
