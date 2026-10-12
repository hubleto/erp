<?php

namespace Hubleto\App\Community\PriceLists\Models;

use Hubleto\App\Community\Documents\Models\Document;
use Hubleto\Framework\Db\Column\Lookup;

class PriceListHasDocument extends \Hubleto\Erp\Model
{
  public string $table = 'price_lists_documents';
  public string $recordManagerClass = RecordManagers\PriceListHasDocument::class;
  public ?string $lookupSqlValue = '{%TABLE%}.id';
  public ?string $lookupUrlAdd = 'price-lists/documents/add';
  public ?string $lookupUrlDetail = 'price-lists/documents/{%ID%}';

  public array $relations = [
    'PRICE_LIST' => [ self::BELONGS_TO, PriceList::class, 'id_price_list', 'id' ],
    'DOCUMENT' => [ self::BELONGS_TO, Document::class, 'id_document', 'id' ],
  ];

  /**
   * Describes columns of the model.
   *
   * @return array
   *
   */
  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_price_list' => (new Lookup($this, $this->translate('Price list'), PriceList::class))->setFkOnDelete('CASCADE')->setRequired()->setDefaultVisible(),
      'id_document' => (new Lookup($this, $this->translate('Document'), Document::class))->setFkOnDelete('CASCADE')->setRequired()->setDefaultVisible(),
    ]);
  }

  /**
   * Describes the table (list) of records.
   *
   * @return \Hubleto\Framework\Description\Table
   *
   */
  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();

    $description->ui['title'] = '';
    $description->ui['addButtonText'] = $this->translate('Add document');
    $description->show(['header', 'fulltextSearch', 'columnSearch', 'moreActionsButton']);

    return $description;
  }

  /**
   * Describes the form of a record.
   *
   * @return \Hubleto\Framework\Description\Form
   *
   */
  public function describeForm(): \Hubleto\Framework\Description\Form
  {
    $description = parent::describeForm();

    $idFromUrl = $this->router()->urlParamAsInteger('idPriceList');
    if ($idFromUrl > 0) $description->defaultValues['id_price_list'] = $idFromUrl;

    return $description;
  }

}
