<?php

namespace Hubleto\App\Community\Tasks;

class Calendar extends \Hubleto\App\Community\Calendar\Calendar
{
  public function getCalendarConfig(): array
  {
    return [
      'position' => 2,
      'color' => '#7a23dc',
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
      ->whereRaw("`{$mTask->table}`.`date_deadline` >= ? AND `{$mTask->table}`.`date_deadline` <= ?", [$dateStart, $dateEnd])
      ->with('PROJECT')
    ;

    if (isset($filter['idUser']) && $filter['idUser'] > 0) {
      $tasks = $tasks->where($mTask->table . '.id_developer', $filter['idUser']);
    }
    if (isset($filter['fCompleted']) && $filter['fCompleted'] > 0) {
      $tasks = $tasks->where($mTask->table . '.is_closed', $filter['fCompleted'] == 2);
    }
    if (isset($filter['fOwnership']) && $filter["fOwnership"] == 1) {
      $tasks = $tasks->where($mTask->table . ".id_developer", $this->getService(\Hubleto\Framework\AuthProvider::class)->getUserId());
    }

    $tasks = $tasks->get();

    foreach ($tasks as $task) { //@phpstan-ignore-line
      $events[] = [
        'id' => (int) ($task->id ?? 0),
        'start' => date("Y-m-d", strtotime($task->date_deadline)),
        'end' => date("Y-m-d", strtotime($task->date_deadline)),
        'allDay' => true,
        'title' => $task->virt_related_to . ' TASK ' . $task->identifier . ' ' . $task->title . '(' . $task->PROJECT->title . ')',
        'color' => $task->is_closed ? '#DDDDDD' : '#1A8404',
        'source' => 'tasks',
        'id_owner' => $task->id_developer,
        'completed' => $task->is_closed,
        'url' => 'tasks/' . $task->id,
      ];
    }

    // todos
    $todos = $mTodo->record->prepareReadQuery()
      ->where($mTodo->table . '.is_closed', 0)
      ->whereRaw("`{$mTodo->table}`.`date_deadline` >= ? AND `{$mTodo->table}`.`date_deadline` <= ?", [$dateStart, $dateEnd])
      ->with('TASK');

    if (isset($filter['idUser']) && $filter['idUser'] > 0) {
      $todos = $todos->where($mTodo->table . '.id_responsible', $filter['idUser']);
    }
    if (isset($filter['fCompleted']) && $filter['fCompleted'] > 0) {
      $todos = $todos->where($mTodo->table . '.is_closed', $filter['fCompleted'] == 2);
    }

    $todos = $todos->get();

    foreach ($todos as $todo) { //@phpstan-ignore-line
      $events[] = [
        'id' => (int) ($todo->id ?? 0),
        'start' => date("Y-m-d", strtotime($todo->date_deadline)),
        'end' => date("Y-m-d", strtotime($todo->date_deadline)),
        'allDay' => true,
        'title' => $todo->TASK->virt_related_to . ' TODO ' . $todo->todo . '(' . $todo->TASK->id . ' ' . $todo->TASK->title . ')',
        'color' => $todo->is_closed ? '#DDDDDD' : '#1A8404',
        'source' => 'tasks',
        'id_owner' => $todo->id_responsible,
        'completed' => $todo->is_closed,
        'url' => 'tasks/todo/' . $todo->id,
      ];
    }


    return $events;
  }

}
