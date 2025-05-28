<?php

namespace HubletoApp\Community\Cloud\Models;

use ADIOS\Core\Db\Column\Integer;
use ADIOS\Core\Db\Column\Date;
use ADIOS\Core\Db\Column\Boolean;
use ADIOS\Core\Db\Column\Decimal;

use HubletoApp\Community\Settings\Models\User;

class Log extends \HubletoMain\Core\Models\Model
{
  public string $table = 'premium_log';
  public string $recordManagerClass = RecordManagers\Log::class;

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'date' => (new Date($this, $this->translate('Date')))->setRequired(),
      'active_users' => (new Integer($this, $this->translate('Active users')))->setRequired(),
      'paid_apps' => (new Integer($this, $this->translate('Paid apps')))->setRequired(),
      'is_premium_expected' => (new Boolean($this, $this->translate('Premium expected')))->setRequired(),
      'is_trial_period' => (new Boolean($this, $this->translate('Is trial period')))->setRequired(),
      'price' => (new Decimal($this, $this->translate('Price'))),
    ]);
  }

  public function describeTable(): \ADIOS\Core\Description\Table
  {
    $description = parent::describeTable();
    $description->columns['id'] = $this->columns['id'];
    $description->permissions['canCreate'] = false;
    $description->permissions['canUpdate'] = false;
    $description->permissions['canDelete'] = false;
    return $description;
  }

}
