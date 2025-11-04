<?php

namespace Hubleto\App\Community\Cashdesk\Models;



use Hubleto\Erp\Model;
use Hubleto\Framework\Db\Column\Boolean;
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

class CashRegister extends Model
{
  public bool $isExtendableByCustomColumns = true;

  public string $table = 'cashdesk_cash_registers';
  public string $recordManagerClass = RecordManagers\CashRegister::class;
  public ?string $lookupSqlValue = '{%TABLE%}.identifier';
  public ?string $lookupUrlDetail = 'cashdesk/cash-registers/{%ID%}';
  public ?string $lookupUrlAdd = 'cashdesk/cash-registers/add';

  public array $relations = [
    'COMPANY' => [ self::BELONGS_TO, Company::class, 'id_company', 'id' ],
  ];

  public function describeColumns(): array
  {
    return array_merge([
      'id_company' => new Lookup($this, $this->translate("Company"), Company::class)->setRequired()->setDefaultVisible(),
      'identifier' => (new Varchar($this, $this->translate('Identifier')))->setRequired()->setDefaultVisible()->setIcon(self::COLUMN_IDENTIFIER_DEFUALT_ICON),
      'description' => (new Varchar($this, $this->translate('Description')))->setRequired()->setDefaultVisible(),
    ], parent::describeColumns());
  }

  public function describeTable(): Table
  {
    $description = parent::describeTable();
    $description->ui['title'] = '';
    $description->ui['addButtonText'] = $this->translate('Add cash register');
    $description->show(['header', 'fulltextSearch']);
    $description->hide(['footer']);
    return $description;
  }

}
