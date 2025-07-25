<?php

namespace HubletoApp\Community\Settings\Models;

use Hubleto\Framework\Db\Column\Varchar;

class Company extends \Hubleto\Framework\Models\Model
{
  public string $table = 'companies';
  public string $recordManagerClass = RecordManagers\Company::class;
  public ?string $lookupSqlValue = '{%TABLE%}.name';

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'name' => (new Varchar($this, $this->translate('Company')))->setRequired(),
      'tax_id' => (new Varchar($this, $this->translate('Tax ID'))),
      'vat_id' => (new Varchar($this, $this->translate('VAT ID'))),
      'street_1' => (new Varchar($this, $this->translate('Street, line 1'))),
      'street_2' => (new Varchar($this, $this->translate('Street, line 2'))),
      'zip' => (new Varchar($this, $this->translate('ZIP'))),
      'city' => (new Varchar($this, $this->translate('City'))),
      'country' => (new Varchar($this, $this->translate('Country'))),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();

    $description->ui['title'] = '';
    $description->ui['addButtonText'] = 'Add company';
    $description->ui['showHeader'] = true;
    $description->ui['showFulltextSearch'] = true;
    $description->ui['showFooter'] = false;

    return $description;
  }

}
