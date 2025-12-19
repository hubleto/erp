<?php

namespace Hubleto\App\Community\Projects\Controllers;

use Hubleto\App\Community\Worksheets\Models\Activity;

class MonthlySummary extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'projects', 'content' => $this->translate('Projects') ],
      [ 'url' => 'projects/monthly-summary', 'content' => $this->translate('Monthly summary') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();

    /** @var Activity */
    $mWorksheet = $this->getModel(Activity::class);
    $worksheetSummary = $mWorksheet->record
      ->selectRaw('
        projects_tasks.id_project,
        projects.title AS project_title,
        customers.name as customer_name,
        YEAR(date_worked) AS year,
        MONTH(date_worked) AS month,
        SUM(worked_hours) AS total_hours
      ')
      ->leftJoin('tasks', 'tasks.id', '=', 'worksheet_activities.id_task')
      ->leftJoin('projects_tasks', 'projects_tasks.id_task', '=', 'tasks.id')
      ->leftJoin('projects', 'projects.id', '=', 'projects_tasks.id_project')
      ->leftJoin('customers', 'customers.id', '=', 'projects.id_customer')
      ->groupByRaw('YEAR(date_worked), MONTH(date_worked), projects_tasks.id_project')
      ->whereNotNull('projects_tasks.id_project')
      ->orderBy('date_worked', 'desc')
      ->get()
    ;

    $summary = [];
    foreach ($worksheetSummary as $row) {

      if (!isset($summary[$row->id_project])){
        $summary[$row->id_project] = [
          'project_title' => $row->project_title,
          'customer_name' => $row->customer_name,
          'summary' => [],
        ];
      }

      if (!isset($summary[$row->id_project]['summary'][$row->year])) {
        $summary[$row->id_project]['summary'][$row->year] = [];
      }

      $summary[$row->id_project]['summary'][$row->year][$row->month] = $row->total_hours;
    }

    $this->viewParams['summary'] = $summary;

    $this->setView('@Hubleto:App:Community:Projects/MonthlySummary.twig');
  }

}
