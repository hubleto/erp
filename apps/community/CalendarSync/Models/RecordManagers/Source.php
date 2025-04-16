<?php

namespace HubletoApp\Community\CalendarSync\Models\RecordManagers;

class Source extends \HubletoMain\Core\RecordManager
{
  public $table = 'calendar_sync_sources';

  public function prepareReadQuery(mixed $query = null, int $level = 0): mixed
  {

    $main = \ADIOS\Core\Helper::getGlobalApp();

    $query = parent::prepareReadQuery($query, $level);
    $type = $main->urlParamAsString('type') ?? "";

    if ($type == 'google') {
      $query = $query->where('type', 'google');
    }
    else if ($type == 'ics') {
      $query = $query->where('type', 'ics');
    }

    return $query;
  }
}