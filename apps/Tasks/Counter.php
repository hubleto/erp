<?php

namespace Hubleto\App\Community\Tasks;

use Hubleto\Erp\Core;

class Counter extends Core
{

  /**
   * [Description for myDueTodo]
   *
   * @return int
   * 
   */
  public function myDueTodo(): int
  {
    $mItem = $this->getModel(Models\Todo::class);
    return $mItem->record->prepareReadQuery()
      ->where('tasks_todo.id_responsible', $this->authProvider()->getUserId())
      ->whereDate('tasks_todo.date_deadline', '<', date("Y-m-d"))
      ->count()
    ;
  }

}
