<?php

namespace Hubleto\App\Community\Tasks;

class Loader extends \Hubleto\Framework\App
{

  /**
   * Inits the app: adds routes, settings, calendars, hooks, menu items, ...
   *
   * @return void
   * 
   */
  public function init(): void
  {
    parent::init();

    $this->router()->get([
      '/^tasks(\/(?<recordId>\d+))?\/?$/' => Controllers\Tasks::class,
    ]);

    $this->addSearchSwitch('t', 'tasks');

    /** @var \Hubleto\App\Community\Workflow\Manager $workflowManager */
    $workflowManager = $this->getService(\Hubleto\App\Community\Workflow\Manager::class);
    $workflowManager->addWorkflow($this, 'tasks', Workflow::class);

  }

  // installTables
  public function installTables(int $round): void
  {
    if ($round == 1) {
      $this->getModel(Models\Task::class)->dropTableIfExists()->install();
    }
  }

  /**
   * Implements fulltext search functionality for tasks
   *
   * @param array $expressions List of expressions to be searched and glued with logical 'or'.
   * 
   * @return array
   * 
   */
  public function search(array $expressions): array
  {
    $mTask = $this->getModel(Models\Task::class);
    $qTasks = $mTask->record->prepareReadQuery();
    
    foreach ($expressions as $e) {
    //   $qTasks = $qTasks->whereRaw('
    //     (
    //       tasks.identifier like ?
    //       or tasks.title like ?
    //       or (
    //         select deals_tasks.id from deals_tasks
    //         left join deals on deals.id = deals_tasks.id_deal
    //         where
    //           deals_tasks.id_task = tasks.id
    //           and deals.title like ?
    //       )
    //       or (
    //         select projects_tasks.id from projects_tasks
    //         left join projects on projects.id = projects_tasks.id_project
    //         where
    //           projects_tasks.id_task = tasks.id
    //           and projects.title like ?
    //       )
    //     )
    //     and not tasks.is_closed
    //   ', [
    //     '%' . $e . '%', '%' . $e . '%', '%' . $e . '%',
    //     '%' . $e . '%'
    //   ]);
      $qTasks = $qTasks->where(function($q) use ($e) {
        $q->orHaving('tasks.identifier', 'like', '%' . $e . '%');
        $q->orHaving('tasks.title', 'like', '%' . $e . '%');
        $q->orHaving('virt_related_to', 'like', '%' . $e . '%');
      })
      ->where('tasks.is_closed', false);
    }

    $tasks = $qTasks->get()->toArray();
    $results = [];

    foreach ($tasks as $task) {
      $results[] = [
        "id" => $task['id'],
        "label" => $task['identifier'] . ' ' . $task['title'],
        "url" => 'tasks/' . $task['id'],
        "description" => $task['virt_related_to'], //($task['PROJECTS'][0]['PROJECT']['title'] ?? ''),
      ];
    }

    return $results;
  }

}
