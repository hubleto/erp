<?php

namespace HubletoApp\Community\CalendarSync\Models\RecordManagers;

class Source extends \HubletoMain\RecordManager
{
  public $table = 'calendar_sync_sources';

  public function prepareReadQuery(mixed $query = null, int $level = 0): mixed
  {

    $main = \HubletoMain\Loader::getGlobalApp();

    $query = parent::prepareReadQuery($query, $level);
    $type = $main->getRouter()->urlParamAsString('type') ?? "";

    if ($type == 'google') {
      $query = $query->where('type', 'google');
    } elseif ($type == 'ics') {
      $query = $query->where('type', 'ics');
    }

    return $query;
  }
}
