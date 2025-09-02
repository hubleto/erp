<?php

namespace Hubleto\App\Community\Orders\Models;

use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\Framework\Db\Column\Decimal;
use Hubleto\Framework\Db\Column\Integer;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\App\Community\Documents\Models\Document;

class OrderDocument extends \Hubleto\Erp\Model
{
  public string $table = 'orders_documents';
  public string $recordManagerClass = RecordManagers\OrderDocument::class;
  public ?string $lookupSqlValue = '{%TABLE%}.id';

  public array $relations = [
    'ORDER'   => [ self::BELONGS_TO, Order::class, 'id_order', 'id'],
    'DOCUMENT' => [ self::BELONGS_TO, Document::class, 'id_document', 'id'],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_order' => (new Lookup($this, $this->translate('Order'), Order::class))->setRequired(),
      'id_document' => (new Lookup($this, $this->translate('Document'), Document::class))->setRequired(),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();

    $description->ui['title'] = 'Order Documents';
    $description->ui["addButtonText"] = $this->translate("Add document");

    if ($this->router()->urlParamAsInteger('idOrder') > 0) {
      $description->columns = [];
      $description->inputs = [];
      $description->ui = [];
    }

    return $description;
  }
}
