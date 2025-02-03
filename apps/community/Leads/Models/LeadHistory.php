<?php

namespace HubletoApp\Community\Leads\Models;

use HubletoApp\Community\Customers\Models\Company;
use HubletoApp\Community\Customers\Models\Person;
use HubletoApp\Community\Leads\Models\Lead;
use HubletoApp\Community\Settings\Models\Currency;
use HubletoApp\Community\Settings\Models\User;

use \ADIOS\Core\Db\Column\Date;
use \ADIOS\Core\Db\Column\Lookup;
use \ADIOS\Core\Db\Column\Varchar;

class LeadHistory extends \HubletoMain\Core\Model
{
  public string $table = 'lead_histories';
  public string $eloquentClass = Eloquent\LeadHistory::class;
  public ?string $lookupSqlValue = '{%TABLE%}.description';

  public array $relations = [
    'LEAD' => [ self::BELONGS_TO, Lead::class, 'id_lead','id'],
  ];

  public function columns(array $columns = []): array
  {
    return parent::columns(array_merge($columns, [
      'change_date' => (new Date($this, $this->translate('Change Date')))->setRequired(),
      'id_lead' => (new Lookup($this, $this->translate('Lead'), Lead::class, 'CASCADE'))->setRequired(),
      'description' => (new Varchar($this, $this->translate('Description')))->setRequired(),
    ]));
  }

  public function tableDescribe(): \ADIOS\Core\Description\Table
  {
    $description = parent::tableDescribe();
    $description->ui['title'] = 'Leads';
    $description->ui['addButtonText'] = 'Add Lead';
    $description->ui['showHeader'] = true;
    $description->ui['showFooter'] = false;
    unset($description->columns['note']);
    return $description;
  }

}
