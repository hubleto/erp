<?php

namespace Hubleto\App\Community\HrRecruitment;

use Hubleto\Erp\Core;

class Counter extends Core
{
  public function inProgressApplications(): int
  {
    $mApplication = $this->getModel(Models\Application::class);

    return $mApplication->record->prepareReadQuery()
      ->whereHas('WORKFLOW_STEP', function ($query) {
        $query->whereNotIn('tag', ['hr-recruitment-hired', 'hr-recruitment-rejected']);
      })
      ->count();
  }
}