<?php

namespace HubletoApp\Community\Goals;

class Loader extends \HubletoMain\Core\App
{

  public function init(): void
  {
    parent::init();

    $this->main->router->httpGet([
      '/^goals?$/' => Controllers\Goals::class,
      '/^goals\/report?$/' => Controllers\ReportGoal::class,
      '/^goals\/report\/get-goal-data?$/' => Controllers\Api\GetGoalData::class,
      '/^goals\/report\/get-interval-data?$/' => Controllers\Api\GetIntervalData::class,
    ]);
  }

  public function installTables(int $round): void
  {
    if ($round == 1) {
      $mGoal = new Models\Goal($this->main);
      $mGoalValue = new Models\GoalValue($this->main);

      $mGoal->dropTableIfExists()->install();
      $mGoalValue->dropTableIfExists()->install();
    }
  }

  public function installDefaultPermissions(): void
  {
    $mPermission = new \HubletoApp\Community\Settings\Models\Permission($this->main);
    $permissions = [];

    foreach ($permissions as $permission) {
      $mPermission->eloquent->create([
        "permission" => $permission
      ]);
    }
  }
}