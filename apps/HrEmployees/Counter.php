<?php

namespace Hubleto\App\Community\HrEmployees;

use Hubleto\Erp\Core;

class Counter extends Core
{
  public function activeEmployees(): int
  {
    $mEmployee = $this->getModel(Models\Employee::class);

    return $mEmployee->record->prepareReadQuery()
      ->where($mEmployee->table . '.employment_status', $this->translate('Active'))
      ->count();
  }
}