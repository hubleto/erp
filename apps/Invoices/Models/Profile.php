<?php

namespace Hubleto\App\Community\Invoices\Models;

use Hubleto\App\Community\Settings\Models\Company;
use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\App\Community\Documents\Models\Template;

class Profile extends \Hubleto\Erp\Model
{
  public string $table = 'invoice_profiles';
  public string $recordManagerClass = RecordManagers\Profile::class;
  public ?string $lookupSqlValue = '{%TABLE%}.name';
  public ?string $lookupUrlDetail = 'invoices/profiles/{%ID%}';
  public ?string $lookupUrlAdd = 'invoices/profiles/add';

  public array $relations = [
    'COMPANY' => [ self::BELONGS_TO, Company::class, "id_company" ],
    'TEMPLATE' => [ self::HAS_ONE, Template::class, 'id', 'id_template'],
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
      'name' => (new Varchar($this, $this->translate('Name'))),
      'id_company' => (new Lookup($this, $this->translate('Company'), Company::class)),
      'numbering_pattern' => (new Varchar($this, $this->translate('Numbering pattern'))),
      'id_template' => (new Lookup($this, $this->translate('Template'), Template::class)),
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

    $description->ui['addButtonText'] = "Add invoice profile";

    return $description;
  }

  /**
   * [Description for describeForm]
   *
   * @return \Hubleto\Framework\Description\Form
   * 
   */
  public function describeForm(): \Hubleto\Framework\Description\Form
  {
    $description = parent::describeForm();

    $description->ui['title'] = 'Invoice profile';

    return $description;
  }

}
