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

    $this->getRouter()->httpGet([
      '/^tasks(\/(?<recordId>\d+))?\/?$/' => Controllers\Tasks::class,
    ]);

    $this->addSearchSwitch('t', 'tasks');

    /** @var \Hubleto\App\Community\Pipeline\Manager $pipelineManager */
    $pipelineManager = $this->getService(\Hubleto\App\Community\Pipeline\Manager::class);
    $pipelineManager->addPipeline($this, 'tasks', Pipeline::class);

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
      $qTasks = $qTasks->where(function($q) use ($e) {
        $q->orWhere('tasks.identifier', 'like', '%' . $e . '%');
        $q->orWhere('tasks.title', 'like', '%' . $e . '%');
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
        "description" => $task['PROJECT']['name'] ?? '',
      ];
    }

    return $results;
  }

}
