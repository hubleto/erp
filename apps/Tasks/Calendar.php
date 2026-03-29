<?php

namespace Hubleto\App\Community\Tasks;

class Calendar extends \Hubleto\App\Community\Calendar\Calendar
{
  public function getCalendarConfig(): array
  {
    return [
      'title' => $this->translate('Tasks'),
      'addNewActivityButtonText' => $this->translate('Add new activity linked to Tasks'),
      'icon' => 'fas fa-calendar',
      'formComponent' => 'TasksFormActivity',
    ];
  }

  public function loadEvent(int $id): array
  {
    return $this->prepareLoadActivityQuery($this->getModel(Models\Todo::class), $id)->first()?->toArray();
  }

  public function loadEvents(string $dateStart, string $dateEnd, array $filter = []): array
  {
    $events = [];

    /** @var Models\Task */
    $mTask = $this->getModel(Models\Task::class);

    /** @var Models\Todo */
    $mTodo = $this->getModel(Models\Todo::class);

    // tasks
    $tasks = $mTask->record->prepareReadQuery()
      ->whereRaw("`{$mTask->table}`.`date_deadline` >= ? AND `{$mTask->table}`.`date_deadline` <= ?", [$dateStart, $dateEnd]);

    if (isset($filter['idUser']) && $filter['idUser'] > 0) {
      $tasks = $tasks->where($mTask->table . '.id_developer', $filter['idUser']);
    }
    if (isset($filter['completed'])) {
      $tasks = $tasks->where('is_closed', $filter['completed']);
    }

    $tasks = $tasks->get();

    foreach ($tasks as $task) { //@phpstan-ignore-line
      $events[] = [
        'id' => (int) ($task->id ?? 0),
        'start' => date("Y-m-d", strtotime($task->date_deadline)),
        'end' => date("Y-m-d", strtotime($task->date_deadline)),
        'allDay' => true,
        'title' => 'TASK ' . $task->identifier . ' ' . $task->title,
        'color' => $task->is_closed ? '#DDDDDD' : '#1A8404',
        'source' => 'tasks',
        'id_owner' => $task->id_developer,
        'completed' => $task->is_closed,
      ];
    }

    // todos
    $todos = $mTodo->record->prepareReadQuery()
      ->whereRaw("`{$mTodo->table}`.`date_deadline` >= ? AND `{$mTodo->table}`.`date_deadline` <= ?", [$dateStart, $dateEnd])
      ->with('TASK');

    if (isset($filter['idUser']) && $filter['idUser'] > 0) {
      $todos = $todos->where($mTodo->table . '.id_responsible', $filter['idUser']);
    }
    if (isset($filter['completed'])) {
      $todos = $todos->where('is_closed', $filter['completed']);
    }

    $todos = $todos->get();

    foreach ($todos as $todo) { //@phpstan-ignore-line
      $events[] = [
        'id' => (int) ($todo->id ?? 0),
        'start' => date("Y-m-d", strtotime($todo->date_deadline)),
        'end' => date("Y-m-d", strtotime($todo->date_deadline)),
        'allDay' => true,
        'title' => 'TODO ' . (string) ($todo->todo ?? ''),
        'color' => $todo->is_closed ? '#DDDDDD' : '#8401A4',
        'source' => 'tasks',
        'id_owner' => $todo->id_responsible,
        'completed' => $todo->is_closed,
      ];
    }


    return $events;
  }

}
