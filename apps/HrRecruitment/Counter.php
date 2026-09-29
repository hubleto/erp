<?php

namespace Hubleto\App\Community\HrRecruitment;

use Hubleto\Erp\Core;

class Counter extends Core
{
  public function inProgressApplications(): int
  {
    $mApplication = $this->getModel(Models\Application::class);

    return $mApplication->record->prepareReadQuery()
      ->where($mApplication->table . '.status', $this->translate('In progress'))
      ->count();
  }
}