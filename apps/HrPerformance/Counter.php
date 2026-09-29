<?php

namespace Hubleto\App\Community\HrPerformance;

use Hubleto\Erp\Core;

class Counter extends Core
{
  public function overdueDevelopmentItems(): int
  {
    $mGoal = $this->getModel(Models\Goal::class);
    $mAssignment = $this->getModel(Models\LearningAssignment::class);
    $today = date('Y-m-d');

    $overdueGoals = $mGoal->record->prepareReadQuery()
      ->whereDate('hr_goals.date_due', '<', $today)
      ->whereIn('hr_goals.status', [
        $this->translate('Not started'),
        $this->translate('In progress'),
      ])
      ->count();

    $overdueLearning = $mAssignment->record->prepareReadQuery()
      ->whereDate('hr_learning_assignments.date_due', '<', $today)
      ->whereIn('hr_learning_assignments.status', [
        $this->translate('Assigned'),
        $this->translate('In progress'),
        $this->translate('Overdue'),
      ])
      ->count();

    return $overdueGoals + $overdueLearning;
  }
}