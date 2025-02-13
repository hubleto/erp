<?php

namespace HubletoApp\Community\Deals\Models;

use HubletoApp\Community\Customers\Models\Customer;
use HubletoApp\Community\Customers\Models\Person;
use HubletoApp\Community\Deals\Models\Deal;
use HubletoApp\Community\Settings\Models\Currency;
use HubletoApp\Community\Settings\Models\User;

use \ADIOS\Core\Db\Column\Lookup;
use \ADIOS\Core\Db\Column\Varchar;
use \ADIOS\Core\Db\Column\Date;

class DealHistory extends \HubletoMain\Core\Model
{
  public string $table = 'deal_histories';
  public string $eloquentClass = Eloquent\DealHistory::class;
  public ?string $lookupSqlValue = '{%TABLE%}.description';

  public array $relations = [
    'DEAL' => [ self::BELONGS_TO, Deal::class, 'id_deal','id'],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'change_date' => (new Date($this, $this->translate('Change Date')))->setRequired(),
      'id_deal' => (new Lookup($this, $this->translate('Deal'), Deal::class, 'CASCADE'))->setRequired(),
      'description' => (new Varchar($this, $this->translate('Description')))->setRequired(),
    ]);
  }

  public function describeTable(): \ADIOS\Core\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['title'] = 'Deals';
    $description->ui['addButtonText'] = 'Add Deal';
    $description->ui['showHeader'] = true;
    $description->ui['showFooter'] = false;
    unset($description->columns['note']);
    return $description;
  }

}
