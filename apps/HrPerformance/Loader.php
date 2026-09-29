<?php

namespace Hubleto\App\Community\HrPerformance;

class Loader extends \Hubleto\Erp\App
{
  public function init(): void
  {
    parent::init();

    $this->router()->get([
      '/^hr-performance\/?$/' => ['controller' => Controllers\Performance::class, 'vars' => ['resource' => 'goals']],
      '/^hr-performance\/(?<resource>goals|reviews|courses|learning)(\/(?<recordId>\d+))?\/?$/' => Controllers\Performance::class,
    ]);

    $menu = $this->getService(\Hubleto\App\Community\Desktop\AppMenuManager::class);
    if ($menu) {
      $menu->addItem($this, 'hr-performance', $this->translate('Performance & development'), 'fas fa-chart-line');
    }

    $workflowManager = $this->getService(\Hubleto\App\Community\Workflow\Manager::class);
    $workflowManager->addWorkflowGroup($this, 'hr_performance', Workflow::class);
  }

  public function renderSecondSidebar(): string
  {
    return '
      ' . $this->secondSidebarTitle() . '
      <div class="app-sidebar-buttons">
        ' . $this->secondSidebarButton('hr-performance/goals', 'fas fa-bullseye', 'Goals') . '
        ' . $this->secondSidebarButton('hr-performance/reviews', 'fas fa-star-half-stroke', 'Reviews') . '
        ' . $this->secondSidebarButton('hr-performance/courses', 'fas fa-book-open', 'Courses') . '
        ' . $this->secondSidebarButton('hr-performance/learning', 'fas fa-graduation-cap', 'Learning assignments') . '
      </div>
    ';
  }

  public function getSidebarBadgeNumber(): int
  {
    $counter = $this->getService(Counter::class);
    return $counter->overdueDevelopmentItems();
  }

  public function installApp(int $round): void
  {
    if ($round === 1) {
      $this->getModel(Models\Goal::class)->upgradeSchema();
      $this->getModel(Models\Review::class)->upgradeSchema();
      $this->getModel(Models\Course::class)->upgradeSchema();
      $this->getModel(Models\LearningAssignment::class)->upgradeSchema();
    }
  }

  public function generateDemoData(): void
  {
    $mUser = $this->getModel(\Hubleto\App\Community\Auth\Models\User::class);
    $user = $mUser->record->where('is_active', true)->orderBy('id')->first();
    if (!$user) return;

    $mGoal = $this->getModel(Models\Goal::class);
    if (!$mGoal->record->where('id_user', $user->id)->where('title', $this->translate('Complete annual HR compliance training'))->exists()) {
      $mGoal->record->recordCreate([
        'id_user' => $user->id,
        'title' => $this->translate('Complete annual HR compliance training'),
        'description' => $this->translate('Complete required workplace and privacy training.'),
        'date_due' => date('Y-m-d', strtotime('+30 days')),
        'progress' => 35,
        'status' => $this->translate('In progress'),
      ]);
    }

    if (!$mGoal->record->where('id_user', $user->id)->where('title', $this->translate('Document employee onboarding process'))->exists()) {
      $mGoal->record->recordCreate([
        'id_user' => $user->id,
        'title' => $this->translate('Document employee onboarding process'),
        'description' => $this->translate('Publish a consistent onboarding checklist for managers.'),
        'date_due' => date('Y-m-d', strtotime('+60 days')),
        'progress' => 10,
        'status' => $this->translate('Not started'),
      ]);
    }

    $mCourse = $this->getModel(Models\Course::class);
    $course = $mCourse->record->where('name', $this->translate('Workplace essentials - Demo'))->first();
    if (!$course) {
      $created = $mCourse->record->recordCreate([
        'name' => $this->translate('Workplace essentials - Demo'),
        'provider' => $this->translate('People Operations'),
        'delivery' => $this->translate('Online'),
        'duration_hours' => 3,
        'url' => 'https://example.test/training/workplace-essentials',
        'is_active' => 1,
        'description' => $this->translate('Demo course covering workplace essentials.'),
      ]);
      $course = $mCourse->record->find($created['id']);
    }

    $securityCourseName = $this->translate('Data privacy refresher - Demo');
    $securityCourse = $mCourse->record->where('name', $securityCourseName)->first();
    if (!$securityCourse) {
      $created = $mCourse->record->recordCreate([
        'name' => $securityCourseName,
        'provider' => $this->translate('Compliance team'),
        'delivery' => $this->translate('Online'),
        'duration_hours' => 1,
        'url' => 'https://example.test/training/data-privacy',
        'is_active' => 1,
        'description' => $this->translate('Short demo course on data privacy and secure handling.'),
      ]);
      $securityCourse = $mCourse->record->find($created['id']);
    }

    $mAssignment = $this->getModel(Models\LearningAssignment::class);
    if (!$mAssignment->record->where('id_user', $user->id)->where('id_course', $course->id)->exists()) {
      $mAssignment->record->recordCreate([
        'id_user' => $user->id,
        'id_course' => $course->id,
        'date_assigned' => date('Y-m-d', strtotime('-10 days')),
        'date_due' => date('Y-m-d', strtotime('-2 days')),
        'status' => $this->translate('In progress'),
        'notes' => $this->translate('Demo learning assignment.'),
      ]);
    }

    if (!$mAssignment->record->where('id_user', $user->id)->where('id_course', $securityCourse->id)->exists()) {
      $mAssignment->record->recordCreate([
        'id_user' => $user->id,
        'id_course' => $securityCourse->id,
        'date_assigned' => date('Y-m-d'),
        'date_due' => date('Y-m-d', strtotime('+21 days')),
        'status' => $this->translate('Assigned'),
        'notes' => $this->translate('Additional demo learning assignment.'),
      ]);
    }

    $period = (string) date('Y') . ' Demo';
    $mReview = $this->getModel(Models\Review::class);
    if (!$mReview->record->where('id_user', $user->id)->where('period', $period)->exists()) {
      $mReview->record->recordCreate([
        'id_user' => $user->id,
        'id_reviewer' => $user->id,
        'period' => $period,
        'date_reviewed' => date('Y-m-d', strtotime('+14 days')),
        'score' => null,
        'status' => $this->translate('Scheduled'),
        'summary' => $this->translate('Demo performance review awaiting completion.'),
      ]);
    }

    $completedPeriod = (string) (date('Y') - 1) . ' Demo';
    if (!$mReview->record->where('id_user', $user->id)->where('period', $completedPeriod)->exists()) {
      $mReview->record->recordCreate([
        'id_user' => $user->id,
        'id_reviewer' => $user->id,
        'period' => $completedPeriod,
        'date_reviewed' => date('Y-m-d', strtotime('-1 year')),
        'score' => 4.2,
        'status' => $this->translate('Completed'),
        'summary' => $this->translate('Demo completed review with strong collaboration and delivery outcomes.'),
      ]);
    }
  }
}