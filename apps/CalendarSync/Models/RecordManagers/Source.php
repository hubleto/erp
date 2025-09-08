<?php

namespace Hubleto\App\Community\CalendarSync\Models\RecordManagers;

class Source extends \Hubleto\Erp\RecordManager
{
  public $table = 'calendar_sync_sources';

  public function prepareReadQuery(mixed $query = null, int $level = 0): mixed
  {

    $main = \Hubleto\Erp\Loader::getGlobalApp();

    $query = parent::prepareReadQuery($query, $level);
    $type = $main->router()->urlParamAsString('type') ?? "";

    if ($type == 'google') {
      $query = $query->where('type', 'google');
    } elseif ($type == 'ics') {
      $query = $query->where('type', 'ics');
    }

    return $query;
  }
}
