<?php

namespace HubletoApp\Community\Cloud\Models;

use Hubleto\Framework\Db\Column\Integer;
use Hubleto\Framework\Db\Column\DateTime;
use Hubleto\Framework\Db\Column\Boolean;
use Hubleto\Framework\Db\Column\Decimal;
use HubletoApp\Community\Settings\Models\User;

class Log extends \Hubleto\Framework\Models\Model
{
  public string $table = 'cloud_log';
  public string $recordManagerClass = RecordManagers\Log::class;

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'log_datetime' => (new DateTime($this, $this->translate('Log datetime')))->setRequired(),
      'active_users' => (new Integer($this, $this->translate('Active users')))->setRequired(),
      'paid_apps' => (new Integer($this, $this->translate('Paid apps')))->setRequired(),
      'is_premium_expected' => (new Boolean($this, $this->translate('Premium expected')))->setRequired(),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->columns['id'] = $this->columns['id'];
    $description->permissions['canCreate'] = false;
    $description->permissions['canUpdate'] = false;
    $description->permissions['canDelete'] = false;
    return $description;
  }

}
