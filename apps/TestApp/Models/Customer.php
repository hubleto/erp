<?php

namespace Hubleto\App\Community\TestApp\Models;



use Hubleto\App\Community\TestApp\Models\Migrations\Migration_24_02_2026_0001_initial;
use Hubleto\Erp\Model;
use Hubleto\Framework\Db\Column\Boolean;
use Hubleto\Framework\Db\Column\Date;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Text;
use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\Framework\Db\Column\Json;
use Hubleto\App\Community\Auth\Models\User;
use Hubleto\App\Community\Contacts\Models\Contact;
use Hubleto\App\Community\Deals\Models\Deal;
use Hubleto\App\Community\Leads\Models\Lead;
use Hubleto\App\Community\Settings\Models\Country;
use Hubleto\Framework\Db\Column\Virtual;
use Hubleto\Framework\Description\Input;
use Hubleto\Framework\Description\Table;
use Hubleto\Framework\Helper;

class Customer extends Model
{
  public bool $isExtendableByCustomColumns = true;

  public string $table = 'test_customers';
  public string $recordManagerClass = RecordManagers\Customer::class;
  public ?string $lookupSqlValue = 'if({%TABLE%}.identifier != "", {%TABLE%}.identifier, {%TABLE%}.name)';
  public ?string $lookupUrlDetail = 'customers/{%ID%}';
  public ?string $lookupUrlAdd = 'customers/add';

  public function describeColumns(): array
  {
    return array_merge([
//      'name' => (new Varchar($this, $this->translate('Name')))->setRequired()->setDefaultVisible()->setIcon(self::COLUMN_NAME_DEFAULT_ICON),
    ], parent::describeColumns());
  }

  public function migrations(): array
  {
    return [
      0 => new Migration_24_02_2026_0001_initial($this->db(), $this),
    ];
  }
}
