<?php

namespace Hubleto\App\Community\Cashdesk\Models;



use Hubleto\Erp\Model;
use Hubleto\Framework\Db\Column\DateTime;
use Hubleto\Framework\Db\Column\Date;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Decimal;
use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\App\Community\Auth\Models\User;
use Hubleto\App\Community\Contacts\Models\Contact;
use Hubleto\App\Community\Deals\Models\Deal;
use Hubleto\App\Community\Leads\Models\Lead;
use Hubleto\App\Community\Settings\Models\Country;
use Hubleto\Framework\Description\Input;
use Hubleto\Framework\Description\Table;
use Hubleto\Framework\Helper;

use Hubleto\App\Community\Settings\Models\Company;

class Receipt extends Model
{
  public bool $isExtendableByCustomColumns = true;

  public string $table = 'cashdesk_receipts';
  public string $recordManagerClass = RecordManagers\Receipt::class;
  public ?string $lookupSqlValue = '{%TABLE%}.number';
  public ?string $lookupUrlDetail = 'cashdesk/receipts/{%ID%}';
  public ?string $lookupUrlAdd = 'cashdesk/receipts/add';

  public array $relations = [
    'COMPANY' => [ self::BELONGS_TO, Company::class, 'id_company', 'id' ],
    'CASH_REGISTER' => [ self::BELONGS_TO, CashRegister::class, 'id_cash_register', 'id' ],
    'ITEMS' => [ self::HAS_MANY, ReceiptItem::class, 'id_receipt', 'id' ],
  ];

  public function describeColumns(): array
  {
    return array_merge([
      'number' => (new Varchar($this, $this->translate('Number')))->setRequired()->setDefaultVisible(),
      'id_company' => new Lookup($this, $this->translate("Company"), Company::class)->setRequired()->setDefaultVisible(),
      'id_cash_register' => new Lookup($this, $this->translate("Cash register"), CashRegister::class)->setRequired()->setDefaultVisible(),
      'total_price_excl_vat' => (new Decimal($this, $this->translate('Total price excl. VAT')))->setDefaultVisible()->setUnit('€'),
      'total_price_incl_vat' => (new Decimal($this, $this->translate('Total price incl. VAT')))->setDefaultVisible()->setUnit('€'),
      'created' => (new DateTime($this, $this->translate('Created')))->setDefaultVisible()->setDefaultValue(date("Y-m-d H:i:s", strtotime("+14 days"))),
    ], parent::describeColumns());
  }

  public function describeTable(): Table
  {
    $description = parent::describeTable();
    $description->ui['title'] = '';
    $description->ui['addButtonText'] = $this->translate('Add receipt');
    $description->show(['header', 'fulltextSearch', 'columnSearch', 'moreActionsButton']);
    $description->hide(['footer']);
    return $description;
  }

}
