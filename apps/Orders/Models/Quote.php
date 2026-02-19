<?php

namespace Hubleto\App\Community\Orders\Models;

use Hubleto\Framework\Db\Column\Date;
use Hubleto\Framework\Db\Column\File;
use Hubleto\Framework\Db\Column\Text;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Varchar;

use Hubleto\App\Community\Invoices\Models\Item;
use Hubleto\App\Community\Orders\Models\Order;
use Hubleto\App\Community\Auth\Models\User;
use Hubleto\Framework\Db\Column\Integer;

class Quote extends \Hubleto\Erp\Model
{
  public string $table = 'orders_quotes';
  public string $recordManagerClass = RecordManagers\Quote::class;
  public ?string $lookupSqlValue = '{%TABLE%}.version';
  public ?string $lookupUrlDetail = 'orders/quotes/{%ID%}';
  public ?string $lookupUrlAdd = 'orders/quotes/add';

  public array $relations = [
    'ORDER' => [ self::BELONGS_TO, Order::class, "id_order" ],
    'APPROVED_BY' => [ self::BELONGS_TO, User::class, 'id_approved_by', 'id'],
    'OWNER' => [ self::BELONGS_TO, User::class, 'id_owner', 'id'],
    'MANAGER' => [ self::BELONGS_TO, User::class, 'id_manager', 'id' ],
  ];

  /**
   * [Description for describeColumns]
   *
   * @return array
   * 
   */
  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_order' => (new Lookup($this, $this->translate('Order'), Order::class)),
      'version' => new Integer($this, $this->translate('Version'))->setDefaultVisible(),
      'date_created' => (new Date($this, $this->translate('Created')))->setDefaultVisible()->setDefaultValue(date("Y-m-d")),
      'date_sent' => (new Date($this, $this->translate('Sent to customer')))->setDefaultVisible(),
      'id_approved_by' => (new Lookup($this, $this->translate('Approved by'), User::class))->setReactComponent('InputUserSelect')->setDefaultVisible(),
      'date_approved' => (new Date($this, $this->translate('Approved')))->setDefaultVisible(),
      'date_accepted' => (new Date($this, $this->translate('Accepted by customer')))->setDefaultVisible(),
      'summary' => (new Text($this, $this->translate('Quote summary')))->setDefaultVisible(),
      'online_document_1' => (new Varchar($this, $this->translate('Online document #1')))->setReactComponent('InputHyperlink'),
      'online_document_2' => (new Varchar($this, $this->translate('Online document #2')))->setReactComponent('InputHyperlink'),
      'online_document_3' => (new Varchar($this, $this->translate('Online document #3')))->setReactComponent('InputHyperlink'),
      'online_document_4' => (new Varchar($this, $this->translate('Online document #4')))->setReactComponent('InputHyperlink'),
      'online_document_5' => (new Varchar($this, $this->translate('Online document #5')))->setReactComponent('InputHyperlink'),
      'final_pdf_1' => (new File($this, $this->translate('Final PDF #1'))),
      'final_pdf_2' => (new File($this, $this->translate('Final PDF #2'))),
      'final_pdf_3' => (new File($this, $this->translate('Final PDF #3'))),
      'final_pdf_4' => (new File($this, $this->translate('Final PDF #4'))),
      'final_pdf_5' => (new File($this, $this->translate('Final PDF #5'))),
      'notes_document_1' => (new Text($this, $this->translate('Notes for document #1'))),
      'notes_document_2' => (new Text($this, $this->translate('Notes for document #2'))),
      'notes_document_3' => (new Text($this, $this->translate('Notes for document #3'))),
      'notes_document_4' => (new Text($this, $this->translate('Notes for document #4'))),
      'notes_document_5' => (new Text($this, $this->translate('Notes for document #5'))),
      'id_owner' => (new Lookup($this, $this->translate('Owner'), User::class))->setReactComponent('InputUserSelect')->setDefaultVisible()->setDefaultValue($this->getService(\Hubleto\Framework\AuthProvider::class)->getUserId()),
      'id_manager' => (new Lookup($this, $this->translate('Manager'), User::class))->setReactComponent('InputUserSelect')->setDefaultVisible()->setDefaultValue($this->getService(\Hubleto\Framework\AuthProvider::class)->getUserId())->setDefaultVisible(),
    ]);
  }

  /**
   * [Description for describeTable]
   *
   * @return \Hubleto\Framework\Description\Table
   * 
   */
  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['addButtonText'] = $this->translate("Add quote");
    $description->show(['header', 'fulltextSearch', 'columnSearch', 'moreActionsButton']);
    $description->hide(['footer']);
    $description->ui['orderBy'] = [
      'field' => 'version',
      'direction' => 'desc',
    ];

    return $description;
  }

}
