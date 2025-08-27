<?php

namespace HubletoApp\Community\Cloud\Models\RecordManagers;

class Payment extends \HubletoMain\RecordManager
{
  public $table = 'cloud_payments';

  public function recordCreate(array $record): array
  {
    $cloudApp = $this->model->main->apps->community('Cloud');
    $isTrialPeriod = $cloudApp->saveConfig('isTrialPeriod', '0');
    return parent::recordCreate($record);
  }

  public function recordDelete(int|string $id): int
  {
    return 0;
  }
}
