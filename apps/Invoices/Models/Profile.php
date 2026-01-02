<?php

namespace Hubleto\App\Community\Invoices\Models;

use Hubleto\App\Community\Settings\Models\Company;
use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\Framework\Db\Column\Boolean;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\App\Community\Documents\Models\Template;
use Hubleto\App\Community\Settings\Models\Currency;
use Hubleto\Framework\Db\Column\Integer;
use Hubleto\Framework\Db\Column\Image;

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
      'name' => (new Varchar($this, $this->translate('Name')))->setDefaultVisible(),
      'headline' => (new Varchar($this, $this->translate('Headline')))->setDefaultVisible(),
      'id_company' => (new Lookup($this, $this->translate('Company'), Company::class))->setDefaultVisible(),
      'numbering_pattern' => (new Varchar($this, $this->translate('Numbering pattern')))
        ->setDescription($this->translate('YYYY - 4-digit year, YY - 2-digit year, MM - 2-digit month, DD - 2-digit day, N(repeated) - incremental invoice number'))
        ->setPredefinedValues([
          'YYYYNNNN',
          'YYNNNNNN',
          'YYMMDDNNNN',
          'YYYY/NNNN',
          '11-YYYY-NN',
        ])
        ->setDefaultVisible()
      ,
      'iban' => (new Varchar($this, $this->translate('Bank account number (IBAN)')))->setDefaultVisible(),
      'swift' => (new Varchar($this, $this->translate('Bank (SWIFT/BIC)')))->setDefaultVisible(),
      'id_currency' => (new Lookup($this, $this->translate('Currency'), Currency::class))->setDefaultVisible(),
      'is_default' => (new Boolean($this, $this->translate('Is default')))->setDefaultVisible(),
      'due_days' => (new Integer($this, $this->translate('Due days')))->setDefaultVisible(),
      'stamp_and_signature' => (new Image($this, $this->translate('Stamp and signature'))),
      'id_template' => (new Lookup($this, $this->translate('Default invoice template'), Template::class)),
      'id_payment_method' => (new Lookup($this, $this->translate('Default payment method'), PaymentMethod::class)),
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
