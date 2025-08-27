<?php

namespace HubletoApp\Community\Settings\Models;

use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\Framework\Db\Column\Lookup;

class InvoiceProfile extends \Hubleto\Erp\Model
{
  public string $table = 'invoice_profiles';
  public ?string $lookupSqlValue = '{%TABLE%}.name';
  public string $recordManagerClass = RecordManagers\InvoiceProfile::class;

  public array $relations = [
    'SUPPLIER' => [ self::BELONGS_TO, Company::class, "id_supplier" ],
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
      'id_supplier' => (new Lookup($this, $this->translate('Supplier'), Company::class)),
      'numbering_pattern' => (new Varchar($this, $this->translate('Numbering pattern'))),
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
