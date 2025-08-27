<?php

namespace HubletoApp\Community\Customers\Models;

use Hubleto\Framework\Db\Column\Lookup;
use HubletoApp\Community\Documents\Models\Document;

class CustomerDocument extends \Hubleto\Erp\Model
{
  public string $table = 'customer_documents';
  public string $recordManagerClass = RecordManagers\CustomerDocument::class;

  public array $relations = [
    'CUSTOMER' => [ self::BELONGS_TO, Customer::class, 'id_customer', 'id' ],
    'DOCUMENT' => [ self::BELONGS_TO, Document::class, 'id_document', 'id' ],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_customer' => (new Lookup($this, $this->translate('Customer'), Customer::class, "CASCADE"))->setRequired(),
      'id_document' => (new Lookup($this, $this->translate('Document'), Document::class, "CASCADE"))->setRequired(),
    ]);
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

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();

    if ($this->getRouter()->urlParamAsInteger('idCustomer') > 0) {
      $description->columns = [];
      $description->inputs = [];
      $description->ui = [];
    }

    return $description;
  }

  public function onBeforeDelete(int $id): int
  {
    $idDocument = (int) $this->record->find($id)->toArray()["id_document"];
    ($this->getService(Document::class))->onBeforeDelete($idDocument);
    ($this->getService(Document::class))->record->where("id", $idDocument)->delete();

    return $id;
  }
}
